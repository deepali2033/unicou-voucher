<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'reason',
        'status',
        'admin_note',
        'bank_details',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
