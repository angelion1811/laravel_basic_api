<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(["email", "password"]);
        try {
            $token = JWTAuth::attempt($credentials);
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
       
        if (!$token) {
            return response()->json([
                "message" => "Credenciales invalidas"
            ],401);   
        }   

        return response()->json([
            "token" => $token
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'          => 'required|alpha_spaces|max:50',
            'email'         => 'required|email|unique:users,email|min:10',
            'password'      => 'required|min:8|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user','token'),201);
    }
}
