<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreated;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when(request('role'), fn($q, $role) => $q->where('role', $role))
            ->when(request('status'), fn($q, $status) => $q->where('status', $status))
            ->when(request('search'), function($q, $search) {
                $q->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->orderBy(request('sort_by', 'created_at'), request('sort_direction', 'desc'))
            ->paginate(request('per_page', 15));
        
        return response()->json($users);
    }

    public function store(Request $request)
    {
   
    try {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['admin', 'user', 'manager', 'superadmin'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 404);
        }

        logger($validated);

        $validated['password'] = Hash::make("password");
        try {
            $user = User::create($validated);

            //send email to user
          try {
            Mail::to($user->email)->send(new UserCreated($user));
          } catch (\Exception $e) {
            logger("Error sending email to user: " . $e->getMessage());
          }



        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 404);
        }
        return response()->json($user, 201);
    }

    public function show(Request $request)
    {
        try {   
            logger($request->user);
            $user = User::findOrFail($request->user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => ['required', 'string', Rule::unique('users')->ignore($request->user)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($request->user)],
            'phone_number' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['admin', 'user', 'manager', 'superadmin'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
    
        try {
            $user = User::findOrFail($request->user);
            $user->update($validated);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function destroy(Request $request)
    {
        try {
            $user = User::findOrFail($request->user);
            logger($user);
           try {
                $user->delete();
                return response()->json(['message' => 'User deleted successfully']);
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function updatePassword(Request $request, User $user)
    {
        logger($request->user());
   
        //check if the current password is correct
        $user = User::findOrFail($request->user);
        if (!Hash::check($request->current_password, $user->password)) {
            logger("Current password is incorrect");

            $errors = [
                'current_password' => 'Le mot de passe actuel est incorrect'
            ];
            return response()->json(['errors' => $errors], 422);

        }
        //check if the new password is correct
        if ($request->new_password !== $request->confirm_password) {
            $errors = [
                'confirm_password' => 'Le mot de passe et la confirmation ne correspondent pas'
            ];
            return response()->json(['errors' => $errors], 422);
        }
        //check if the new password is different from the current password
        if ($request->new_password === $request->current_password) {
            $errors = [
                'new_password' => 'Le nouveau mot de passe ne peut pas être le même que le mot de passe actuel'
            ];
            return response()->json(['errors' => $errors], 422);
        }

        try {
            $user = User::findOrFail($request->user);
            $user->password = Hash::make($request->new_password);
            $user->save();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        return response()->json(['message' => 'Mot de passe mis à jour avec succès']);
    }

    public function updateProfile(Request $request, User $user)
    {
        logger($request->user());

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($request->user)],
            'phone_number' => 'nullable|string|max:20',
        ]);

        try {
            $user = User::findOrFail($request->user);
            $user->update($validated);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        return response()->json(['message' => 'Profil mis à jour avec succès']);
    }
} 