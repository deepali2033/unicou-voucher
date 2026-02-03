<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function referral()
    {
        $user = Auth::user();
        $referralHistory = Order::where('user_id', $user->id)
            ->where('referral_points', '>', 0)
            ->latest()
            ->paginate(15);

        $totalPoints = Order::where('user_id', $user->id)->sum('referral_points');

        return view('dashboard.referral-point.index', compact('referralHistory', 'totalPoints'));
    }
}
