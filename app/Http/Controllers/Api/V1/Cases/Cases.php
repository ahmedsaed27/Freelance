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
use Spatie\Activitylog\Models\Activity;

class Cases extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cases = ModelsCases::with('user' ,'city', 'caseKeyword' , 'caseSkill')->paginate(10);

        return $this->successPaginated(status:Response::HTTP_OK , message:'Cases Retrieved Successfully' , data:$cases);
    }

    public function getAllDataWithoutPaginate(){
        $data = ModelsCases::with('user' ,'city', 'caseKeyword' , 'caseSkill')->get();

        return $this->success(status: Response::HTTP_OK, message: 'Cases Retrieved Successfully.', data: $data);
    }

    public function getLogs(string $id){
        $data = ModelsCases::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', ModelsCases::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified Case."
            );
        }


        return $this->success(
            status:Response::HTTP_OK
            , message:'Logs Retrived Succesfuly'
            , data:  $logs
        );
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
            $case->caseKeyword()->sync($request->input('keywords'));
            $case->caseSkill()->sync($request->input('skills'));

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
        $case = ModelsCases::with('user' ,'city' ,'caseKeyword' , 'caseSkill')->find($id);

        if (!$case) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Cases not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'Cases Retrieved Successfully.', data: $case);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RequestsCases $request, string $id)
    {
        try{
            $case = ModelsCases::find($id);

            if (!$case) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Cases not found.',);
            }

            if($case->user_id != auth()->guard('api')->id()){
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Only User Who Created The Case Can Update It.',);
            }

            DB::beginTransaction();
            $case->update($request->except('id' , 'certificate'));
            $case->caseKeyword()->sync($request->input('keywords'));
            $case->caseSkill()->sync($request->input('skills'));

            if($request->hasFile('id') && $request->hasFile('certificate')){
                $case->clearMediaCollection('case');
                $case->addMediaFromRequest('id')->toMediaCollection('case', 'cases');
                $case->addMediaFromRequest('certificate')->toMediaCollection('case', 'cases');
            }

            DB::commit();


            return $this->success(status: Response::HTTP_OK, message: 'Cases Updated Successfully.', data: $case);
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
        $case = ModelsCases::find($id);

        if(!$case){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Cases not found.',);
        }

        if($case->user_id != auth()->guard('api')->id()){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Only User Who Created The Case Can Delete It.',);
        }

        if($case->getMedia('case')){
            $case->clearMediaCollection('case');
        }

        $case->delete();
        DB::table('case_keyword')
            ->where('case_id', $case->id)
            ->update(['case_keyword.deleted_at' => now()]);

        DB::table('case_skill')
            ->where('case_id', $case->id)
            ->update(['case_skill.deleted_at' => now()]);

        return $this->success(status: Response::HTTP_OK, message: 'Cases Deleted Successfully.', data: $case);

    }


    public function getCaseByToken(){

        $case = ModelsCases::with('user')->where('user_id' , auth()->guard('api')->id())->first();

        if (!$case) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Cases not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'Cases Retrieved Successfully.', data: $case);
    }


    public function restore(string $id)
    {
        $data = ModelsCases::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Case not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Case not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'Case restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = ModelsCases::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'Case Retrived Succesfuly'
            , data: $data
        );
    }
}
