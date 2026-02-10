<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'voucher_type',
        'voucher_id',
        'quantity',
        'amount',
        'status',
        'user_role',
        'referral_points',
        'delivery_details',
        'payment_method',
        'admin_payment_method_id',
        'payment_receipt',
        'bank_name',
        'account_number',
        'ifsc_code',
        'client_name',
        'client_email',
        'points_earned',
        'points_redeemed',
        'bonus_amount',
        'sub_agent_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Vouchar::class, 'voucher_id', 'voucher_id');
    }

    public function subAgent()
    {
        return $this->belongsTo(User::class, 'sub_agent_id');
    }
}
