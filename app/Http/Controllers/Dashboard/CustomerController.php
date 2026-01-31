<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SupportQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function supportindex()
    {
        return view('dashboard.Customer-support.add-query');
    }

    public function storeSupportQuery(Request $request)
    {
        $request->validate([
            'topic' => 'required|string',
            'issue' => 'required|string',
            'description' => 'required|string',
        ]);

        SupportQuery::create([
            'user_id' => Auth::id(),
            'topic' => $request->topic,
            'issue' => $request->issue,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Your query has been submitted successfully. Our team will get back to you soon.');
    }
}
