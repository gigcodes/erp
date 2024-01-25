<?php

namespace App\Helpers;

class JsonResponse
{
    /**
     * Generate a success response.
     *
     * @param string $message
     * @param array $data
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success(string $message = '', array $data = [], int $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Generate an error response.
     *
     * @param  string  $message
     * @param  array  $errors
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error(string $message = '', array $errors = [], int $statusCode = 400): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
