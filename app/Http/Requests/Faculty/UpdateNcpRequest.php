<?php
// app/Http/Requests/Faculty/UpdateNcpRequest.php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNcpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('faculty')->check();
    }

    public function rules(): array
    {
        return [
            // Basic details
            'patient_name'        => ['required', 'string', 'max:255'],
            'encounter_id'        => ['nullable', 'integer', 'exists:chartings_encounters,id'],
            'noted_at'            => ['required', 'date'],

            // Nursing Diagnoses (NANDA)
            'dx_primary'          => ['required', 'string'],
            'dx_related_to'       => ['nullable', 'string'],
            'dx_as_evidenced_by'  => ['nullable', 'string'],

            // Goals
            'goal_short'          => ['nullable', 'string'],
            'goal_long'           => ['nullable', 'string'],

            // Interventions / Evaluation
            'interventions'       => ['required', 'string'],
            'evaluation'          => ['nullable', 'string'],

            // Optional notes
            'remarks'             => ['nullable', 'string', 'max:2000'],

            // --- Lifecycle fields ---
            'status'              => ['nullable', 'in:active,discharged'],
            'discharged_at'       => ['nullable', 'date'],
            'archived_at'         => ['nullable', 'date'],
            'archived_by'         => ['nullable', 'integer', 'exists:faculty,id'],
            'archive_reason'      => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Convert empty strings to null to keep DB columns consistent
        $this->merge(array_map(
            fn($v) => $v === '' ? null : $v,
            $this->all()
        ));
    }
}