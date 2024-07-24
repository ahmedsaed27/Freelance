<?php

namespace App\Http\Controllers\Api\V1\Verification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\VerificatioRequest;
use App\Models\Verification as VerificationModel;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class Verification extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $verification = VerificationModel::with('user')->paginate(10);

        return $this->successPaginated(status:Response::HTTP_OK , message:'verifications Retrieved Successfully.' , data:$verification);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VerificatioRequest $request)
    {
        try{
            DB::beginTransaction();

            $request->merge([
                'user_id' => auth()->guard('api')->id()
            ]);

            $verification = VerificationModel::create($request->except('attachments'));

            DB::commit();

            $verification->addMediaFromRequest('attachments')->toMediaCollection('verifications', 'verifications');

            return $this->success(status:Response::HTTP_OK , message:'verifications Created Successfully.' , data:$verification);

        }catch(Exception $e){
            DB::rollBack();

            $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $verifications = VerificationModel::with('user')->find($id);

        if (!$verifications) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'verifications not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'verifications Retrieved Successfully.', data: [
            'verifications' => $verifications,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VerificatioRequest $request, string $id)
    {
        $verification = VerificationModel::find($id);

        if (!$verification) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'verifications not found.',);
        }

        if($verification->user_id != auth()->guard('api')->id()){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Only User Who Create The verification Can Update.',);
        }

        $verification->update($request->except('attachments'));


        if($request->hasFile('attachments')){
            $verification->clearMediaCollection('verifications');
            $verification->addMediaFromRequest('attachments')->toMediaCollection('verifications', 'verifications');
        }



        return $this->success(status: Response::HTTP_OK, message: 'verification Updated Successfully.', data: [
            'profile' => $verification,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $verification = VerificationModel::find($id);


        if (!$verification) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'verifications not found.',);
        }

        if($verification->user_id != auth()->guard('api')->id()){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Only User Who Create The verification Can Update.',);
        }

        $verification->clearMediaCollection('verifications');

        $verification->delete();

        return $this->success(status: Response::HTTP_OK, message: 'verification Deleted Successfully.',data:$verification);
    }
}
