<?php

namespace App\Http\Controllers;

trait ApiResponse
{
    protected function success($data, int $statusCode = 200)
    {
        if (isset($data['pagination'])) {
            $pagination = $data['pagination'];
            $data = $data['data'];

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'pagination' => $pagination,
            ], $statusCode);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], $statusCode);
    }

    protected function error(string $message, int $statusCode = 400, ?array $details = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($details) {
            $response['details'] = $details;
        }

        return response()->json($response, $statusCode);
    }
}
