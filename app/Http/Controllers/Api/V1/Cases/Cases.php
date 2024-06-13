<?php

namespace App\Http\Controllers\Api\V1\Cases;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Cases as RequestsCases;
use App\Models\Cases as ModelsCases;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class Cases extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cases = ModelsCases::with('user' ,'receive' ,'city', 'media')->paginate(10);

        return $this->success(status:Response::HTTP_OK , message:'Cases Retrieved Successfully' , data:$cases);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RequestsCases $request)
    {
        try{
            $request->merge([
                'user_id' => auth()->guard('api')->id()
            ]);

            DB::beginTransaction();

            $case = ModelsCases::create($request->except('id' , 'certificate'));

            if($request->hasFile('id') && $request->hasFile('certificate')){
                $case->addMediaFromRequest('id')->toMediaCollection('case', 'cases');
                $case->addMediaFromRequest('certificate')->toMediaCollection('case', 'cases');
            }

            DB::commit();

            return $this->success(status:Response::HTTP_OK , message:'Cases Created Successfully' , data:$case);

        }catch(Exception $e){
            DB::rollBack();

            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());

        }




    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $case = ModelsCases::with('user')->where('user_id' , auth()->guard('api')->id())->where('id' , $id)->first();

        if (!$case) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Cases not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Retrieved Successfully.', data: $case);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try{
            $case = ModelsCases::where('user_id' , auth()->guard('api')->id())->where('id' , $id)->first();

            if (!$case) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Cases not found.',);
            }

            DB::beginTransaction();
            $case->update($request->except('attachments'));


            if($request->hasFile('attachments')){
                $case->clearMediaCollection('case');
                $case->addMediaFromRequest('attachments')->toMediaCollection('case', 'cases');
                $case->getMedia('cases');
            }

            DB::commit();


            return $this->success(status: Response::HTTP_OK, message: 'Profiles Updated Successfully.', data: $case);
        }catch(Exception $e){
            DB::rollBack();

            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $case = ModelsCases::where('user_id' , auth()->guard('api')->id())->where('id' , $id)->first();

        if(!$case){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Cases not found.',);
        }

        if($case->getMedia('case')){
            $case->clearMediaCollection('case');
        }

        $case->delete();

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Deleted Successfully.', data: $case);

    }
}
