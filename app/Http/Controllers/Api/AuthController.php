<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Subscription;

class AuthController extends Controller
{
    /**
     * Login the user.
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse The login response.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        //get the user
        try {
            $user = User::where('email', $request->email)->first();
            } catch (\Exception $e) {
                logger($e->getMessage());
            }

        if (!$user) {
            return response()->json([
                'message' => 'email ou mot de passe incorrect.',
                'errors' => ['email' => ['email ou mot de passe incorrect.']]
            ], 401);
        }

        //check if the user exists and the password is correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'email ou mot de passe incorrect.',
                'errors' => [
                    'email' => ['email ou mot de passe incorrect.']
                ]
            ], 401);
        }

        // Delete existing tokens
        $user->tokens()->delete();

        // Create new token with expiration
        $token = $user->createToken($request->device_name, ['*'], now()->addHour());

        try {
            //get the user with the subscription
            $subscription = Subscription::with('user')->where('user_id', $user->id)->first();
        } catch (\Exception $e) {
            logger($e->getMessage());
        }
        

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user,
            'expires_at' => $token->accessToken->expires_at
        ]);
    }

    /**
     * Logout the authenticated user.
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse The logout response.
     */
    public function logout(Request $request)
    {
        //delete the current token
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get the authenticated user.
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse The authenticated user.
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
} 