<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\PasswordLinkSentRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{   
    public $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        $this->model->create($validated);

        return response()->json([
            'message' => 'You registered successfully',
        ], 200);

    }

    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();

        if(\Auth::attempt($validated))
        {
            $authenticated_user = \Auth::user();
            $user = User::find($authenticated_user->id);

            $tokenName = $request['username']."_auth_login_token_";
            $token = \Auth::user()->createToken($tokenName)->accessToken;

            return response()->json([
                'Token' => $token,
                'Type' => 'Bearer',
            ], 200);
        }

        return response()->json(['error' => 'Unauthorised'], 401); 
    }

    public function logout()
    {
        auth()->user()->token()->delete();
        return response()->json(['success' => 'Logged out'], 401);
    }

    public function passwordEmail(ResetPasswordRequest $request)
    {
        $email = $request->validated();
        
        $status = Password::sendResetLink($email);

        if($status === Password::RESET_LINK_SENT)
        {
            return response()->json(['message' => __($status)], 200);
        }

        throw ValidationException::withMessages([
            'email' => __($status)
        ]);
    }

    public function passwordReset(PasswordLinkSentRequest $request)
    {
        // $data = $request->validated();

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirm', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password
                ])->setRememberToken(\Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );

        if($status == Password::PASSWORD_RESET) {
			return response()->json(['message' => __($status)], 200);
		}
         
        throw ValidationException::withMessages([
            'email' => __($status)
        ]);
    }
}
