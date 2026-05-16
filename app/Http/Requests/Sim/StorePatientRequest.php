<?php

namespace App\Http\Requests\Sim;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool { return auth('faculty')->check(); }

    public function rules(): array
    {
        return [
            'full_name'            => ['required','string','max:191'],
            'demographics_json'    => ['nullable','array'],
            'clinical_history_json'=> ['nullable','array'],
        ];
    }
}
