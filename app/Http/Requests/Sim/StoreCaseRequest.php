<?php

namespace App\Http\Requests\Sim;

use Illuminate\Foundation\Http\FormRequest;

class StoreCaseRequest extends FormRequest
{
    public function authorize(): bool { return auth('faculty')->check(); }

    public function rules(): array
    {
        return [
            'title'   => ['required','string','max:191'],
            'summary' => ['required','string'],
            'patient' => ['nullable','array'],
            'history' => ['nullable','array'],
        ];
    }
}