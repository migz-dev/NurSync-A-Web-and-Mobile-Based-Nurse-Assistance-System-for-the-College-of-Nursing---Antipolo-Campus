<?php

namespace App\Http\Requests\Sim;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'note_format' => ['required', Rule::in(['narrative','soap','dar'])],
            'content'     => ['required','string'],
            'recorded_at' => ['nullable','date'],
        ];
    }
}