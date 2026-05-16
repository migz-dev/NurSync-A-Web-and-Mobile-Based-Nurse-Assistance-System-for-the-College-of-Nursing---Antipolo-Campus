<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimCaseStoreRequest extends FormRequest
{
    public function authorize(): bool { return auth('faculty')->check(); }

    public function rules(): array
    {
        return [
            'title'            => 'required|string|max:255',
            'summary'          => 'nullable|string',
            'chief_complaint'  => 'nullable|string|max:255',
            'primary_dx'       => 'nullable|string|max:255',
            'allergies_json'   => 'nullable|array',
            'allergies_json.*' => 'string|max:120',
            'precautions_json'   => 'nullable|array',
            'precautions_json.*' => 'string|max:120',
            'is_active'        => 'sometimes|boolean',
        ];
    }
}
