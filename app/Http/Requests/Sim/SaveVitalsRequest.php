<?php

namespace App\Http\Requests\Sim;

use Illuminate\Foundation\Http\FormRequest;

class SaveVitalsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'vitals'      => ['required','array'], // {bp, hr, rr, temp, o2, pain, ...}
            'notes'       => ['nullable','string'],
            'recorded_at' => ['nullable','date'],
        ];
    }
}