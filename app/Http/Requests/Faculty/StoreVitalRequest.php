<?php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;

class StoreVitalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('faculty')->check();
    }

    protected function prepareForValidation(): void
    {
        // Treat empty string as null so "nullable" works
        if ($this->has('encounter_id') && $this->input('encounter_id') === '') {
            $this->merge(['encounter_id' => null]);
        }
    }

    public function rules(): array
    {
        return [
            // EITHER provide an existing encounter...
            'encounter_id' => ['nullable', 'integer', 'exists:chartings_encounters,id'],

            // ...OR create one inline (required_when missing encounter_id)
            'quick_patient.last_name' => ['required_without:encounter_id', 'string', 'max:100'],
            'quick_patient.first_name' => ['required_without:encounter_id', 'string', 'max:100'],
            'quick_encounter.unit' => ['required_without:encounter_id', 'string', 'max:100'],
            'quick_encounter.started_at' => ['required_without:encounter_id', 'date'],
            'quick_encounter.remarks' => ['nullable', 'string', 'max:500'],

            // Vitals
            'taken_at' => ['required', 'date'],
            'temp_c' => ['nullable', 'numeric', 'between:30,45'],
            'pulse_bpm' => ['nullable', 'integer', 'between:20,220'],
            'resp_rate' => ['nullable', 'integer', 'between:5,80'],
            'bp_systolic' => ['nullable', 'integer', 'between:50,260'],
            'bp_diastolic' => ['nullable', 'integer', 'between:20,180'],
            'spo2' => ['nullable', 'integer', 'between:0,100'],
            'pain_scale' => ['nullable', 'integer', 'between:0,10'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'quick_patient.last_name.required_without' => 'Patient last name is required when no encounter is selected.',
            'quick_patient.first_name.required_without' => 'Patient first name is required when no encounter is selected.',
            'quick_encounter.unit.required_without' => 'Unit/Area is required when no encounter is selected.',
            'quick_encounter.started_at.required_without' => 'Encounter start time is required when no encounter is selected.',
        ];
    }
}