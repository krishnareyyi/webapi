<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminAccountsController extends Controller
{
    function login(Request $request)
    {
        $user= User::where('email', $request->email)->where('usertype', 'org')->first();
        // print_r($data);
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response([
                    'message' => 'These credentials do not match our records.',
                    'status'=>'faild'
                ]);
            }
            $token = $user->createToken('my-app-token')->plainTextToken;
            $response = [
                'status'=>'success',
                'user' => $user,
                'token' => $token
            ];
             return response($response);
    }
}
