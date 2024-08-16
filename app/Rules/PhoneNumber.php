<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements Rule
{
    public function passes($attribute, $value)
    {
        // Удалим все нецифровые символы
        $value = preg_replace('/\D/', '', $value);

        // Проверим, что номер телефона начинается с 7 или 8 и содержит 10 цифр
        return preg_match('/^(7|8)\d{10}$/', $value);
    }

    public function message()
    {
        return 'The :attribute is not a valid phone number. It must start with 7 or 8 followed by 10 digits.';
    }
}
