<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function successResponse($message = 'Success',$code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => [],
        ], $code);
    }

     public function successResponseWithData($data=[],$message='success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function errorResponse($error, $code = 404, $errorMessages = [])
    {
        $response = [
            'status' => false,
            'message' => $error
        ];

        if(!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);

    }
}
