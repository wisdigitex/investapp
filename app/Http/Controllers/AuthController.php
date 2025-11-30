<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    // -------------------------------------------
    // REGISTER
    // -------------------------------------------
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'referral_code' => 'nullable|string'
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        // Generate unique referral code
        $refCode = strtoupper(Str::random(8));

        // Check referrer (optional)
        $referredBy = null;
        if ($request->referral_code) {
            $ref = User::where('referral_code', $request->referral_code)->first();
            if ($ref) $referredBy = $ref->id;
        }

        // Create User
        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'referral_code'   => $refCode,
            'referred_by'     => $referredBy,
            'main_balance'    => 0,
            'earnings_balance'=> 0,
            'profile_level_id'=> 1,
        ]);

        // Assign default role
        $user->assignRole('user');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Account created',
            'user' => $user,
            'token' => $token
        ]);
    }

    // -------------------------------------------
    // LOGIN
    // -------------------------------------------
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token
        ]);
    }

    // -------------------------------------------
    // TELEGRAM LOGIN
    // -------------------------------------------
    public function telegramLogin(Request $request)
    {
        $data = $request->initData;

        // VALIDATION: SECURITY CHECK
        if (!$data) {
            return response()->json(['message' => 'Missing initData'], 400);
        }

        $tgData = json_decode($data, true);

        if (!isset($tgData['user']['id'])) {
            return response()->json(['message' => 'Invalid telegram payload'], 400);
        }

        $telegramId = $tgData['user']['id'];
        $name = $tgData['user']['first_name'] ?? 'User';

        // Find or create the user
        $user = User::where('telegram_id', $telegramId)->first();

        if (!$user) {
            $user = User::create([
                'name'            => $name,
                'telegram_id'     => $telegramId,
                'referral_code'   => strtoupper(Str::random(8)),
                'profile_level_id'=> 1,
                'main_balance'    => 0,
                'earnings_balance'=> 0,
            ]);

            $user->assignRole('user');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user
        ]);
    }
}
