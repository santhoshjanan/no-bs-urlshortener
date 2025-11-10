<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiShortenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'string',
                'max:2048',
                'regex:/^https?:\\/\\//i',
                'url',
            ],
        ];
    }

    public function withValidator($validator)
    {
        // rate limiting should be enforced at route/middleware level (10 requests/minute)
    }
}
