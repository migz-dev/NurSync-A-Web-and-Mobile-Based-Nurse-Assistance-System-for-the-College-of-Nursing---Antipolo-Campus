<?php

namespace App\Http\Requests\Sim;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCaseRequest extends FormRequest
{
    public function authorize(): bool { return auth('faculty')->check(); }

    public function rules(): array
    {
        return [
            'title'   => ['sometimes','required','string','max:191'],
            'summary' => ['sometimes','required','string'],
            'patient' => ['nullable','array'],
            'history' => ['nullable','array'],
        ];
    }
}