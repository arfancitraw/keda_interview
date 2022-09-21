<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResources;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends Controller 
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() 
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'unauthorized']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if(!$token = auth('api')->attempt($validator->validated())) {
            return $this->unauthorized();
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return new UserResources(auth('api')->user());
    }

    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'user_type_id' => 'required|exists:user_types,id',
            'password' => 'required|string',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        return response()->json([
            'success' => "User successfully registered",
            'user' => $user,
        ], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() 
    {
        auth('api')->logout();

        return response()->json(['success' => "Successfully logged out"]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Delete a Customer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        $mail = $request->email;
        $customer = User::where('email', $mail)->first();

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$customer) {
            return response()->json([
                'status'    => false,
                'message'   => 'User not found'
            ],404);
        }
        if ($customer->user_type_id != 1) {
            return response()->json([
                'status'    => false,
                'message'   => 'User is not a customer user'
            ],403);
        }else{
            $customer->delete();
            return response()->json([
                'status'    => true,
                'message'   => 'Customer successfully deleted'
            ],200);
        }
        return $this->unauthorized();
    }

    public function getCustomerList() 
    {
        if(auth('api')->user()) {
            return response()->json(['customers' => User::where('user_type_id', 1)->get()]);
        }
        return $this->unauthorized();
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);
    }
}
