<?php

namespace App\Helpers;

class CountryHelper
{
    public static function getCountryData($countryCode)
    {
        $data = [
            'PK' => [
                'currency' => 'PKR',
                'currency_symbol' => 'Rs.',
                'flag' => 'pk',
                'id_label' => 'CNIC',
                'id_placeholder' => '12345-1234567-1',
                'banks' => [
                    'Summit Bank', 'JS Bank', 'EasyPaisa', 'UPaisa', 'Askari Bank', 
                    'Habib Metro Bank', 'Meezan Bank', 'Bank Islami', 
                    'Bank AL Habib Ltd.', 'Zindigi', 'JazzCash'
                ]
            ],
            'IN' => [
                'currency' => 'INR',
                'currency_symbol' => '₹',
                'flag' => 'in',
                'id_label' => 'Aadhar/PAN',
                'id_placeholder' => 'Enter Aadhar or PAN Number',
                'banks' => [
                    'State Bank of India', 'HDFC Bank', 'ICICI Bank', 'Axis Bank', 
                    'Punjab National Bank', 'Bank of Baroda', 'Paytm Payments Bank'
                ]
            ],
            'BD' => [
                'currency' => 'BDT',
                'currency_symbol' => '৳',
                'flag' => 'bd',
                'id_label' => 'NID',
                'id_placeholder' => 'Enter National ID',
                'banks' => [
                    'Sonali Bank', 'Dutch-Bangla Bank', 'bKash', 'Nagad', 
                    'BRAC Bank', 'Islami Bank'
                ]
            ],
            'AE' => [
                'currency' => 'AED',
                'currency_symbol' => 'DH',
                'flag' => 'ae',
                'id_label' => 'Emirates ID',
                'id_placeholder' => 'Enter Emirates ID',
                'banks' => ['Emirates NBD', 'ADCB', 'First Abu Dhabi Bank', 'Mashreq Bank']
            ],
            'DEFAULT' => [
                'currency' => 'USD',
                'currency_symbol' => '$',
                'flag' => 'us',
                'id_label' => 'National ID',
                'id_placeholder' => 'Enter ID Number',
                'banks' => ['Global Bank 1', 'Global Bank 2']
            ]
        ];

        return $data[strtoupper($countryCode)] ?? $data['DEFAULT'];
    }
}
