<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request){
        $validated = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if($validated->fails()){
            return response()->json(['errors' => $validated->errors()], 422);
        }

        // Authentication logic
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $token = Auth::user()->createToken('authToken')->plainTextToken;
            return response()->json([
                'success'=> true, 
                'token' => $token
            ],200);
        } else {
            return response()->json(['errors' => ['Invalid credentials']], 401);
        }
    }
}
