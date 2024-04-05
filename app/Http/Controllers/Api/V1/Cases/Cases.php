<?php

namespace App\Http\Controllers\APi\V1\Cases;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Cases as RequestsCases;
use App\Models\Cases as ModelsCases;
use App\Traits\Api\V1\Responses;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;


class Cases extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cases = ModelsCases::with('user')->paginate(10);

        return $this->success(status:Response::HTTP_OK , message:'Cases Retrieved Successfully' , data:$cases);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RequestsCases $request)
    {
        $request->merge([
            'user_id' => auth()->guard('api')->id()
        ]);

        $case = ModelsCases::create($request->except('attachments'));

        $case->addMediaFromRequest('attachments')->toMediaCollection('case', 'cases');

        $case->getMedia('cases');

        return $this->success(status:Response::HTTP_OK , message:'Cases Created Successfully' , data:$case);

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
        $case = ModelsCases::where('user_id' , auth()->guard('api')->id())->where('id' , $id)->first();

        if (!$case) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Cases not found.',);
        }

        $case->update($request->except('attachments'));


        $case->clearMediaCollection('case');
        $case->addMediaFromRequest('attachments')->toMediaCollection('case', 'cases');

        $case->getMedia('cases');

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Updated Successfully.', data: $case);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $case = ModelsCases::where('user_id' , auth()->guard('api')->id())->where('id' , $id)->first();

        if (!$case) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Cases not found.',);
        }

        $case->delete();

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Deleted Successfully.', data: $case);

    }
}
