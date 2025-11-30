<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileUpgradeController extends Controller
{
    public function upgrade(Request $request)
    {
        $request->validate(['level_id' => 'required|exists:profile_levels,id']);

        $user = auth()->user();
        $level = ProfileLevel::find($request->level_id);

        if ($user->main_balance < $level->upgrade_price) {
            return response()->json(['error' => 'Insufficient balance'], 422);
        }

        // Deduct cost
        $user->main_balance -= $level->upgrade_price;
        $user->profile_level_id = $level->id;
        $user->save();

        return response()->json(['message' => 'Profile upgraded successfully']);
    }

}
