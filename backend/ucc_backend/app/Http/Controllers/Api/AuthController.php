<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTException;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class AuthController extends Controller
{
    //
public function register(StoreUserRequest $request)
{
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    try {
        $token = JWTAuth::fromUser($user);
    } catch (JWTException $e) {
        return response()->json(['error' => 'Could not create token'], 500);
    }

    return response()->json([
        'status' => 'success',
        'token' => $token,
        'user' => $user,
    ], 200);
}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $token = Auth::attempt($credentials);
        if (! $token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unathorized',
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'status' => 'succes',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            return response()->json(['status' => 'error', 'message' => 'Hiba történt'], 500);
        }

        return response()->json(['message' => 'Sikeres kilépés'], 201);
    }

    public function me()
    {
        try {

            $user = Auth::user();
            if (! $user) {

                return response()->json(['status' => 'error', 'message' => 'Felhasználó nem található'], 404);
            }

            return response()->json($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Hiba történt az adatok lekérése közben'], 500);
        }
    }


public function update(UpdateUserRequest $request)
{
    $user = $request->user();

    $validated = $request->validated();

    $user->name = $validated['name'];

    if (!empty($validated['new_password'])) {
        $user->password = Hash::make($validated['new_password']);
    }

    $user->save();

    return response()->json([
        'message' => 'Profil frissítve!',
        'user' => $user
    ]);
}

    // jelszó visszaállítás

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $customClaims = [
            'email' => $user->email,
            'exp' => now()->addMinutes(15)->timestamp,
        ];
        $token = JWTAuth::claims($customClaims)->fromUser($user);

        Mail::to($user->email)->send(new PasswordResetMail($token, $user->email));

        return response()->json(['message' => 'Password reset email sent']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
        ]);

        try {
            $payload = JWTAuth::setToken($request->token)->getPayload();
            $email = $payload['email'] ?? null;
        } catch (JWTException $e) {
            return response()->json(['message' => 'Invalid or expired token'], 400);
        }

        if (! $email) {
            return response()->json(['message' => 'Invalid token payload'], 400);
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password successfully reset']);
    }
}
