<?php
namespace App\Http\Traits;
trait ApiResponseTrait
{
    public function ApiResponse($status = 200, $message = null, $error = null, $data = null)
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if (is_null($data) && !is_null($error)) {
            $response['error'] = $error;
        } elseif (!is_null($data) && is_null($error)) {
            $response['data'] = $data;
        }
        return response($response, $status);
    }
}
