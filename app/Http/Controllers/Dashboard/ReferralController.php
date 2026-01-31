<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReferralController extends Controller
{
    public function referral()
    {
        return view('dashboard.referral-point.index');
    }
}
