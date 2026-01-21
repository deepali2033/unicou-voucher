<?php

namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class Agentcontroller extends Controller
{
    public function dashboard()
    {
        return view('agent.dashboard');
    }

    public function orderHistory()
    {
        return view('agent.order_history');
    }

    public function vouchers()
    {
        return view('agent.vouchers');
    }

    public function banks()
    {
        return view('agent.banks');
    }
}
