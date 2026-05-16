<?php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;

class StoreTreatmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('faculty')->check();
    }

    public function rules(): array
    {
        return [
            // --- Quick patient & encounter ---
            'quick_patient.last_name'    => ['bail', 'required', 'string', 'max:100'],
            'quick_patient.first_name'   => ['bail', 'required', 'string', 'max:100'],
            'quick_patient.hospital_no'  => ['nullable', 'string', 'max:50'],

            'quick_encounter.unit'       => ['bail', 'required', 'string', 'max:120'],
            'quick_encounter.started_at' => ['required', 'date'],
            'quick_encounter.remarks'    => ['nullable', 'string', 'max:1000'],

            // --- Treatment fields ---
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
     * Pre-sanitize and normalize data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'quick_patient' => [
                'last_name'   => trim((string) data_get($this, 'quick_patient.last_name')),
                'first_name'  => trim((string) data_get($this, 'quick_patient.first_name')),
                'hospital_no' => trim((string) data_get($this, 'quick_patient.hospital_no')),
            ],
            'quick_encounter' => [
                'unit'       => trim((string) data_get($this, 'quick_encounter.unit')),
                'remarks'    => trim((string) data_get($this, 'quick_encounter.remarks')),
                'started_at' => data_get($this, 'quick_encounter.started_at'),
            ],
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