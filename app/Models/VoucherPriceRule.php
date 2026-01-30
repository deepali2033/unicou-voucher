<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherPriceRule extends Model
{
    protected $fillable = [
        'inventory_voucher_id',
        'country_code',
        'country_name',
        'sale_price',
        'discount_type',
        'discount_value'
    ];

    public function inventoryVoucher(): BelongsTo
    {
        return $this->belongsTo(InventoryVoucher::class);
    }

    public function getFinalPriceAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return $this->sale_price - ($this->sale_price * ($this->discount_value / 100));
        }
        return $this->sale_price - $this->discount_value;
    }
}
