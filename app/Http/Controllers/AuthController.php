<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User
     */
    public function create(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(),
            [
                'name' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name'       => $request->name,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password)
            ]);

            $user->consumer()->create([
                "billing_address" => '',
                "dob"             => null,
                "barangay"        => '',
                "sitio"           => '',
                "meter_id"        => uniqid(),
                "phone_2"         => '',
                "phone"           => '',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
            [
                'username' => 'required',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::where('username', $request->username)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // Authentication successful
                // $token = $this->generateToken(); // Implement your token generation logic
                // return response()->json(['token' => $token]);
                return response()->json([
                    'status' => true,
                    'message' => 'User Logged In Successfully',
                    'token' => $this->generateToken($user),
                    'data' => $user
                ], 200);
            }else{
                return response()->json([
                    'status'  => false,
                    'message' => 'Check your username & password!',
                ], 500);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    private function generateToken(User $user)
    {
        // This is a simple example; you should use a more secure method for production
        return base64_encode($user->username . ':' . $user->password);
    }
}
