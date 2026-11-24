<?php

namespace App\Http\Requests\Concerns;

trait SanitizesInput
{
    /**
     * @param  list<string>  $fields
     * @return array<string, string>
     */
    protected function sanitizedStrings(array $fields): array
    {
        $sanitized = [];

        foreach ($fields as $field) {
            $value = $this->input($field);

            if (is_string($value)) {
                $sanitized[$field] = trim(strip_tags($value));
            }
        }

        return $sanitized;
    }
}
