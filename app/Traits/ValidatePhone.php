<?php

namespace App\Traits;

use Illuminate\Validation\ValidationException;
use libphonenumber\PhoneNumberUtil;
use Propaganistas\LaravelPhone\PhoneNumber;

trait ValidatePhone
{
    public function validateAndTransformPhone($input, $key = 'phone'): array
    {
        if (isset($input[$key])) {
            try {
                $phoneNumberUtil = PhoneNumberUtil::getInstance();
                $phoneNumberObject = $phoneNumberUtil->parse($input[$key], 'TH');
                $country = $phoneNumberUtil->getRegionCodeForNumber($phoneNumberObject);

                $phoneNumber = new PhoneNumber($input[$key], $country);
                $input[$key] = $phoneNumber->formatE164();
            } catch (\Throwable $exception) {
                throw ValidationException::withMessages([$key => __('validation.phone')]);
            }
        }

        return $input;
    }
}
