<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->response('Unauthorized', 401);
        }

        return $this->response('Authorized', 200, [
            'token' => $request->user()->createToken('token', ['invoice-store', 'invoice-update', 'invoice-delete'])->plainTextToken,
        ]);

    }

    public function logout()
    {
    }
}
