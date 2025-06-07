<?php

namespace App\Services;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneService
{
    public static function passes($attribute, $value, $returnMessage = true)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $numberProto = $phoneUtil->parse($attribute, null);
            $res = $phoneUtil->isValidNumber($numberProto);
            if (!$res) {
                if ($returnMessage) {
                    MessageService::abort(
                        422,
                        'messages.phone.invalid',
                    );
                }
                return false;
            }
            return true;
        } catch (\Exception $e) {
            if ($returnMessage) {
                MessageService::abort(
                    422,
                    'messages.phone.invalid',
                );
            }
            return false;
        }
    }

    public static function parsePhoneParts($rawPhone, $returnMessage = true)
    {
        $rawPhone = str_replace(' ', '', $rawPhone);

        PhoneService::passes($rawPhone, null, $returnMessage);

        $phoneUtil = PhoneNumberUtil::getInstance();
        $number = $phoneUtil->parse($rawPhone, null);

        return [
            'country_code' => $number->getCountryCode(),
            'national_number' => $number->getNationalNumber(),
            'formatted' => $phoneUtil->format($number, PhoneNumberFormat::E164),
        ];
    }
}
