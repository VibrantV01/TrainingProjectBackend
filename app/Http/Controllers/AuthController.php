<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmail;

use App\Models\User;

class AuthController extends Controller
{

    /**
  * Request an email verification email to be sent.
  *
  * @param  Request  $request
  * @return Response
  */

  public function __construct()
     {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout', 'emailVerify']]);//Logout should not be there.
     }


  public function emailRequestVerification(Request $request)
  {

    
    if ( $request->user()->hasVerifiedEmail() ) {
        return response()->json('Email address is already verified.');
    }
    
    $request->user()->sendEmailVerificationNotification();
    
    return response()->json('Email request verification sent to '. Auth::user()->email);
  }
/**
  * Verify an email using email and token from email.
  *
  * @param  Request  $request
  * @return Response
  */
  public function emailVerify(Request $request)
  {
    // return response()->json(auth()->user());
    //$this->validate($request, ['token' => 'required']);
     \Tymon\JWTAuth\Facades\JWTAuth::getToken();
     \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
     
if ( !$request->user() ) {
        return response()->json('Invalid token', 401);
    }
    
    if ( $request->user()->hasVerifiedEmail() ) {
        return response()->json('Email address '.$request->user()->getEmailForVerification().' is already verified.');
    }
$request->user()->markEmailAsVerified();
return response()->json('Email address '. $request->user()->email.' successfully verified.');
  }



    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
     

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // $user = $request->user();
        // if (!$user['email_verified_at']){
        //     return response()->json('verify email first');
        // }
        
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
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
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }
}
