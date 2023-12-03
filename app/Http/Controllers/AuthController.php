<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator=Validator::make($request->all(),['name'=>'required|string|max:255','email'=>'required|string|email|unique:users','password'=>'required|string|min:6']);
        if($validator->fails()){
            return response()->json(['errors'=> $validator->errors()],422);
        }
        $user=User::create(['name'=>$request->name,'email'=>$request->email,'password'=>bcrypt($request->password)]);
        return response()->json(['user'=>$user],201);
    }
    public function login(Request $request){
        $validator=Validator::make($request->all(),['email'=>'required|string|email','password'=>'required|string|min:6']);
        if($validator->fails()){
            return response()->json(['errors'=> $validator->errors()],422);
        }
        $credentials=$request->only(['email','password']);
        if(!Auth::attempt($credentials)){
            return response()->json(['errors'=>'No autorizado'],401);
        }
        $user=$request->user();
        $token=$user->createToken('auth-token')->plainTextToken;
        // $token=$user->createToken('auth.token')->accessToken;
        return response()->json(['Token'=>$token,'Usuario'=>$user],200);
    }
    public function logout(){
        Auth::logout();
        return response()->json(['message' => 'Logout successful']);
    }
}
