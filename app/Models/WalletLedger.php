<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletLedger extends Model
{
    protected $table = 'wallet_ledger';
    protected $fillable = ['user_id', 'type', 'amount', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
