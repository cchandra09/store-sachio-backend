<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use Hash;
use Auth;
class UserController extends Controller
{
    public function register(Request $request)
    {

        try{
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email','max:255', 'unique:users'],
                'password' => ['required', 'string', new password]
            ]);

            $user = new User();
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);

            $user->save();
            $user->fresh();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');
            
        }catch(\Exception $e){
            
            return ResponseFormatter::error([
                'message' => 'Something went Wrong.',
                'error' => $e->getMessage()
            ], 'Authentication Failed', 500);
        }

    }

    public function login(Request $request)
    {

        try{

            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->first();

            if(!Hash::check($request->password, $user->password, [])){
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

        }catch(\Throwable $e){
            return ResponseFormatter::error([
                'message' => 'Something went Wrong.',
                'error' => $e->getMessage()
            ], 'Authentication Failed', 500);
        }

    }

    public function fetch(Request $request)
    {

        $user = $request->user();

        return ResponseFormatter::success($request->user(), 'Data Profile user berhasil di ambil');
    }

    public function updateProfile(Request $request)
    {

        $data = $request->all();
        $user = Auth::user();
        $user = User::findOrFail($user->id)->first()->fill($request->all())->save();

        return ResponseFormatter::success($user, 'Data Profile user berhasil di update');

    }

    public function logout(Request $request)
    {

        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
        
    }
}
