<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait ResponseTrait
{
    protected function handleServiceResponse($result, $success_message = 'success',$success_code =200,$error_code =500)
    {
        if ($result['code'] == 0) {
            Log::error('Error: ' . $result['msg']);

            return response()->json([
                'code' => $error_code,
                'status' => false,
                'message' => $result['msg'],
                'data' => null,
            ], $error_code);
        }

        return response()->json([
            'code' => $success_code,
            'status' => true,
            'message' => $success_message,
            'data' => $result['data'],
        ], $success_code);
    }
}
