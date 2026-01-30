<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletLedger;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('account_type', '!=', 'admin')->get();
        $ledger = WalletLedger::with('user')->latest()->limit(20)->get();

        $stats = [
            'total_balance' => User::where('account_type', '!=', 'admin')->sum('wallet_balance'),
            'total_credits' => WalletLedger::where('type', 'credit')->sum('amount'),
            'total_debits' => WalletLedger::where('type', 'debit')->sum('amount'),
        ];

        return view('dashboard.wallet.index', compact('users', 'ledger', 'stats'));
    }

    public function credit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string'
        ]);

        $user = User::findOrFail($request->user_id);
        $newBalance = $user->wallet_balance + $request->amount;
        $user->update(['wallet_balance' => $newBalance]);

        WalletLedger::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => $request->amount,
            'description' => $request->note ?? 'Manual Credit',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Wallet credited successfully.']);
        }

        return back()->with('success', 'Wallet credited successfully.');
    }

    public function debit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string'
        ]);

        $user = User::findOrFail($request->user_id);
        
        if ($user->wallet_balance < $request->amount) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Insufficient balance.'], 422);
            }
            return back()->with('error', 'Insufficient balance.');
        }

        $newBalance = $user->wallet_balance - $request->amount;
        $user->update(['wallet_balance' => $newBalance]);

        WalletLedger::create([
            'user_id' => $user->id,
            'type' => 'debit',
            'amount' => $request->amount,
            'description' => $request->note ?? 'Manual Debit',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Wallet debited successfully.']);
        }

        return back()->with('success', 'Wallet debited successfully.');
    }
}
