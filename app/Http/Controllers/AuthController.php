<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Validator; 

class AuthController extends Controller
{
    use Authenticatable; 

    public function __construct()
    {
        $this->middleware('auth:api')->except(['login', 'register']);
    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'alert' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }
        
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save(); 
        
        return response()->json([
            'alert' => 'success',
            'message' => 'User created successfully',
            'user' => $user
        ],201);
        
    } 
    
    public function login(Request $request) 
    {
        $validator = Validator::make($request->all(),[ 
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'alert' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }
        
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json([
                'alert' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }
        
        return $this->createNewToken($token);
    }
    
    public function createNewToken($token)
    { 
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }

    public function profile(){
        
        if (!auth()->check()) {
            return response()->json([
                'alert' => 'error',
                'message' => 'You are not authenticated.'
            ], 401);
        }

        $user = auth()->user();

        return response()->json([
            'user' => $user
        ]);
        
    }

    public function logout(){
        auth()->logout();

        return response()->json([
            'alert' => 'success',
            'message' => 'User Logout Success', 
        ]);
    }
    
}