<?php

namespace App\Http\Requests\Sim;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool { return auth('faculty')->check(); }

    public function rules(): array
    {
        return [
            'full_name'            => ['sometimes','required','string','max:191'],
            'demographics_json'    => ['sometimes','nullable','array'],
            'clinical_history_json'=> ['sometimes','nullable','array'],
        ];
    }
}
