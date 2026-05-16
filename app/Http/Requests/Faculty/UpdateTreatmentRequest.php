<?php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTreatmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('faculty')->check();
    }

    public function rules(): array
    {
        return [
            'procedure_name'    => ['bail', 'required', 'string', 'max:180'],
            'indication'        => ['nullable', 'string', 'max:200'],
            'consent_obtained'  => ['nullable', 'boolean'],
            'sterile_technique' => ['nullable', 'boolean'],
            'started_at'        => ['nullable', 'date'],
            'ended_at'          => ['nullable', 'date', 'after_or_equal:started_at'],
            'performed_by'      => ['nullable', 'string', 'max:160'],
            'assisted_by'       => ['nullable', 'string', 'max:160'],
            'outcome'           => ['nullable', 'string', 'max:5000'],
            'complications'     => ['nullable', 'string', 'max:5000'],
            'remarks'           => ['nullable', 'string', 'max:2000'],
            'pre_notes'         => ['nullable', 'string', 'max:2000'],
            'post_notes'        => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Normalize and clean the request before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'procedure_name'    => trim((string) $this->procedure_name),
            'indication'        => trim((string) $this->indication),
            'performed_by'      => trim((string) $this->performed_by),
            'assisted_by'       => trim((string) $this->assisted_by),
            'outcome'           => trim((string) $this->outcome),
            'complications'     => trim((string) $this->complications),
            'remarks'           => trim((string) $this->remarks),
            'pre_notes'         => trim((string) $this->pre_notes),
            'post_notes'        => trim((string) $this->post_notes),
            'consent_obtained'  => (bool) $this->boolean('consent_obtained'),
            'sterile_technique' => (bool) $this->boolean('sterile_technique'),
        ]);
    }
}