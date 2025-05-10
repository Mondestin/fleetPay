<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Subscription;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreated;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Setting;
use App\Mail\NotificationMail;
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

    public function register(Request $request)
    {
          
    try {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 404);
        }
        
        $password = $validated['password'];
        try {
            $user = User::create(
                [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'username' => $validated['email'],
                    'email' => $validated['email'],
                    'role' => 'manager',
                    'status' => 'active',
                    'phone_number' => $validated['phone_number'],
                    'password' => Hash::make($validated['password']),
                ]);

            //send emails
            try {

               Mail::to($user->email)->send(new UserCreated($user, $password));
                logger("mail sent to user: " . $user->email);

                //send email to admin
                $adminEmail = env('MAIL_TO_ADMIN');
                Mail::to($adminEmail)->send(new NotificationMail($user));
                logger("mail sent to admin: " . $adminEmail);

            } catch (\Exception $e) {
            logger("Error sending email to user: " . $e->getMessage());
            }
            //create a company for the user
            try {
                Company::create([
                    'id' => Str::uuid(),
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'status' => 'active',
                    'user_id' => $user->id
                ]);
            } catch (\Exception $e) {
                logger("Error creating company for user: " . $e->getMessage());
            }

            //create subscription and invoice for the user
            try {
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'status' => 'active',
                    'start_date' => now(),
                    'end_date' => now()->addDays(14),
                    'amount' => 0.00,
                    'payment_status' => 'paid',
                    
                ]);

                //create invoice for the user
                Invoice::create([
                    'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . strtoupper(Invoice::count() + 1),
                    'status' => 'paid',
                    'amount' => 0.00,
                    'subscription_id' => $subscription->id,
                    'issue_date' => now(),
                    'due_date' => now()->addDays(7)
                ]);

            } catch (\Exception $e) {
                logger("Error creating subscription for user: " . $e->getMessage());
            }

            //create commission for the user
            try {
                Setting::create([
                    'user_id' => $user->id,
                    'name' => 'commission',
                    'value' => 0.00,
                ]);
            } catch (\Exception $e) {
                logger("Error creating commission for user: " . $e->getMessage());
            }

        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création de l\'utilisateur'], 404);
        }
        
        // Delete existing tokens
        $user->tokens()->delete();

        // Create new token with expiration
        $token = $user->createToken("unknown", ['*'], now()->addHour());

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
} 
