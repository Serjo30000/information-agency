<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class FullName implements Rule
{
    /**
     * Определяем, пройдёт ли атрибут валидацию.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Разбиваем строку на слова
        $words = explode(' ', trim($value));

        // Считаем количество слов
        $wordCount = count($words);

        // Проверяем, чтобы слов было 2 или 3
        return $wordCount === 2 || $wordCount === 3;
    }

    /**
     * Получаем сообщение об ошибке валидации.
     *
     * @return string
     */
    public function message()
    {
        return 'Поле :attribute должно содержать 2 или 3 слова.';
    }
}
