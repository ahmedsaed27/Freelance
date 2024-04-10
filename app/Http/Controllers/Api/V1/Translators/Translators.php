<?php

namespace App\Http\Controllers\Api\V1\Translators;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Translators as TranslatorsRequest;
use App\Models\Translators as TranslatorsModel;
use App\Traits\Api\V1\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Translators extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Translators = TranslatorsModel::with('media')->paginate(10);

        return $this->success(status:Response::HTTP_OK , message:'Translators Retrieved Successfully.' , data:$Translators);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TranslatorsRequest $request)
    {
        
        TranslatorsModel::create($request->except(''));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
