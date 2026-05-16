<?php

namespace App\Http\Requests\Sim;

use Illuminate\Foundation\Http\FormRequest;

class StoreSimPatientRequest extends FormRequest
{
    public function authorize(): bool { return auth('faculty')->check(); }

    public function rules(): array
    {
        return [
            'full_name'        => ['required','string','max:191'],
            'demographics'     => ['nullable','array'],
            'clinical_history'  => ['nullable','array'],
        ];
    }
}