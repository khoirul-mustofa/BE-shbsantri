<?php

namespace App\Response;

use Illuminate\Http\JsonResponse;

class CustomsResponse
{
    public static function success($data = null, $message = 'Success', $status = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error( $errors = null,$message = 'Error', $status = 400): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
