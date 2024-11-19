<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected function jsonResponse($success = true, $data = null, $message = '', $statusCode = 200)
    {
        return response()->json([
            'success' => $success,
            'status' => $statusCode,
            'data' => $data,
            'message' => $message
        ], $statusCode);
    }
}
