<?php

namespace App\Http\Requests\Sim;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveMarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'drug_name'       => ['required','string','max:191'],
            'dose'            => ['nullable','string','max:50'],
            'route'           => ['nullable','string','max:30'],
            'frequency'       => ['nullable','string','max:50'],
            'admin_status'    => ['required', Rule::in(['given','held','refused','error'])],
            'administered_at' => ['nullable','date'],
            'remarks'         => ['nullable','string'],
        ];
    }
}