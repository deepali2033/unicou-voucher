<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class BonusController extends Controller
{
    public function bonus()
    {
        $user = Auth::user();
        $bonusHistory = Order::where('user_id', $user->id)
            ->where('bonus_amount', '>', 0)
            ->latest()
            ->paginate(15);

        $totalBonus = Order::where('user_id', $user->id)->sum('bonus_amount');

        return view('dashboard.bonus-point.index', compact('bonusHistory', 'totalBonus'));
    }
}
