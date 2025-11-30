<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function myInvestments()
{
    $user = auth()->user();

    $investments = UserInvestment::with('package')
        ->where('user_id', $user->id)
        ->orderBy('id', 'desc')
        ->get();

    return response()->json($investments);
}



}
