<?php

namespace App\Http\Requests\Sim;

use Illuminate\Foundation\Http\FormRequest;

class SaveCarePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'nanda_diagnosis_code' => ['nullable','string','max:50'],
            'goals'        => ['nullable','array'],
            'interventions'=> ['nullable','array'],
            'evaluation'   => ['nullable','array'],
        ];
    }
}