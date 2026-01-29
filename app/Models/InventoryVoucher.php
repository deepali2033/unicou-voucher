<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryVoucher extends Model
{
    protected $table = 'inventory_vouchers';

    protected $fillable = [
        'sku_id',
        'brand_name',
        'country_region',
        'currency',
        'voucher_variant',
        'voucher_type',
        'purchase_invoice_no',
        'purchase_date',
        'logo',
        'quantity',
        'purchase_value',
        'purchase_value_per_unit',
        'taxes',
        'local_currency',
        'bank',
        'currency_conversion_rate',
        'referral_points_reseller',
        'agent_referral_points_per_unit',
        'agent_bonus_points_per_unit',
        'agent_sale_price',
        'student_referral_points_per_unit',
        'student_bonus_points_per_unit',
        'student_sale_price',
        'quarterly_points',

        'original_price',
        'upload_vouchers',
        'status'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'quantity' => 'integer',
        'purchase_value' => 'decimal:2',
        'purchase_value_per_unit' => 'decimal:2',
        'taxes' => 'decimal:2',
        'currency_conversion_rate' => 'decimal:6',
        'referral_points_reseller' => 'integer',
        'agent_referral_points_per_unit' => 'integer',
        'agent_bonus_points_per_unit' => 'integer',
        'agent_sale_price' => 'decimal:2',
        'student_referral_points_per_unit' => 'integer',
        'student_bonus_points_per_unit' => 'integer',
        'student_sale_price' => 'decimal:2',


        'original_price' => 'decimal:2',
    ];
}
