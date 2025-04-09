<?php

namespace App\Services;

class HelperService
{
    // get currency symbol from currency code
    public static function getCurrencySymbol($currencyCode): string
    {
        $currencies = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'AUD' => 'A$',
            'CAD' => 'C$',
            'CHF' => 'CHF',
            'CNY' => '¥',
            'AED' => 'د.إ',
            'INR' => '₹',
        ];

        return $currencies[$currencyCode] ?? $currencyCode;
    }
}
