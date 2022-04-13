<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function response_maker($success,$result,$message,$code){
        $response = [
            'success' => $success,
            'data' => $result,
            'messasge' => $message
        ];

        return response()->json($response,$code);
    }
}
