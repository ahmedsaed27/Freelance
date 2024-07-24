<?php

namespace App\Http\Controllers\Api\V1\Documents;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Docs as DocsRequest;
use App\Models\Documents as ModelsDocuments;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Activitylog\Models\Activity;

class Documents extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $docs = ModelsDocuments::with('user')->paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'Document Retrieved Successfully.', data: $docs);
    }

    public function getAllDataWithoutPaginate(){
        $data = ModelsDocuments::with('user')->get();

        return $this->success(status: Response::HTTP_OK, message: 'Currency Retrieved Successfully.', data: $data);
    }

    public function getLogs(string $id){
        $data = ModelsDocuments::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', ModelsDocuments::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified Document."
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
    public function store(DocsRequest $request)
    {
        try{
            $request->merge([
                'user_id'=> auth()->guard('api')->id()
            ]);
            $case = ModelsDocuments::create($request->except('attachments'  , 'demo_file' , 'final_file'));

            $case->addMediaFromRequest('attachments')->toMediaCollection('docs', 'docs');
            $case->addMediaFromRequest('demo_file')->toMediaCollection('docs', 'docs');
            $case->addMediaFromRequest('final_file')->toMediaCollection('docs', 'docs');

            return $this->success(status:Response::HTTP_OK , message:'Document Created Successfully' , data:[
                $case,
            ]);
        }catch(Exception $e){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $docs = ModelsDocuments::with('user')->where('id' , $id)->first();

        if (!$docs) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'docs not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'Document Retrieved Successfully.', data: [
            'docs' => $docs,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DocsRequest $request, string $id)
    {
        $docs = ModelsDocuments::where('id' , $id)->first();

        if (!$docs) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'docs not found.',);
        }

        if($docs->user_id != auth()->guard('api')->id()){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Only User Who Created The Docs Can Update It.',);
        }

        $docs->update($request->except('attachments' , 'demo_file' , 'final_file'));

        $docs->clearMediaCollection('docs');
        $docs->addMediaFromRequest('attachments')->toMediaCollection('docs', 'docs');
        $docs->addMediaFromRequest('demo_file')->toMediaCollection('docs', 'docs');
        $docs->addMediaFromRequest('final_file')->toMediaCollection('docs', 'docs');

        return $this->success(status: Response::HTTP_OK, message: 'Document Updated Successfully.', data: [
            'docs' => $docs,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $docs = ModelsDocuments::where('id' , $id)->first();

        if (!$docs) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'docs not found.',);
        }

        if($docs->user_id != auth()->guard('api')->id()){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Only User Who Created The Docs Can Update It.',);
        }

        $docs->clearMediaCollection('docs');

        $docs->delete();

        return $this->success(status: Response::HTTP_OK, message: 'Document Deleted Successfully.', data: [
            'docs' => $docs,
        ]);
    }


    public function restore(string $id)
    {
        $data = ModelsDocuments::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Document not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Document not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'Document restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = ModelsDocuments::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'Document Retrived Succesfuly'
            , data: $data
        );
    }
}
