<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiController as ApiController;

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class LoginController extends ApiController
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function create_user(Request $request)
    {
        $register_data = $request->validate([
            'name' => ['required','String'],
            'email' => ['required', 'email','unique:App\Models\User,email'],
            'password' => ['required'],
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        $success = true;
        $data = [
            'token'=> $user->createToken('auth-token')->plainTextToken,
            'name' => $user->name,
            'email' => $user->email,
            'password' => $request->password
        ];
        $message = "User successfully created";
        $code = 200;

        return $this->response_maker($success, $data, $message, $code);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $success = true;
            $data = [
                'token'=> $user->createToken('auth-token')->plainTextToken,
                'name' => $user->name
            ];
            $message = "user logged";
            $code = 200;
        } else {
            $success = false;
            $data = [];
            $message = "user not found";
            $code = 401;
        }

        return $this->response_maker($success, $data, $message, $code);
    }

    public function get_user(Request $request)
    {
        $success = true;
        $data = $request->user();
        $message = "Logged user details";
        $code = 200;
        return $this->response_maker($success, $data, $message, $code);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        $success = true;
        $data = [];
        $message = "User token succesfully removed!";
        $code = 200;

        return $this->response_maker($success, $data, $message, $code);
    }
}
