<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Webhook;

class WebhookController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'webhook_name'   => 'required|string',
            'webhook_url'    => 'required|url',
            'webhook_secret' => 'required|string',
            'status'         => 'required'
        ]);

        Webhook::updateOrCreate(
            ['id' => 1], // single webhook rakhna ho to
            [
                'name'   => $request->webhook_name,
                'url'    => $request->webhook_url,
                'secret' => $request->webhook_secret,
                'status' => $request->status
            ]
        );

        return back()->with('success', 'Webhook saved successfully');
    }
}
