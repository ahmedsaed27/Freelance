<?php

namespace  App\Traits\Api\V1;

trait Responses{


    public function success(int $status = 200 , string $message  , array|object $data){
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ] , $status);
    }


    public function error(int $status = 500 , string $message){
        return response()->json([
            'status' => $status,
            'message' => $message,
        ] , $status);
    }
}
