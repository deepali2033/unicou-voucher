<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function bonus()
    {
        return    view('dashboard.bonus-point.index');
    }
}
