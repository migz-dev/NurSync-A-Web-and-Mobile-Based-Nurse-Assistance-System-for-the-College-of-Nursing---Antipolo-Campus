<?php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;

class StoreIntakeOutputRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated faculty can store I&O records
        return auth('faculty')->check();
    }

    public function rules(): array
    {
        return [
            /* ------------------------------
             | Quick Patient (inline create)
             * ------------------------------ */
            'quick_patient.last_name'   => ['required', 'string', 'max:100'],
            'quick_patient.first_name'  => ['required', 'string', 'max:100'],
            'quick_patient.hospital_no' => ['nullable', 'string', 'max:50'],

            /* ------------------------------
             | Quick Encounter (inline create)
             * ------------------------------ */
            'quick_encounter.unit'       => ['required', 'string', 'max:120'],
            'quick_encounter.started_at' => ['required', 'date'],
            'quick_encounter.remarks'    => ['nullable', 'string', 'max:1000'],

            /* ------------------------------
             | I&O Measurements
             * ------------------------------ */
            'started_at' => ['nullable', 'date'],
            'ended_at'   => ['nullable', 'date', 'after_or_equal:started_at'],

            'intake_oral_ml'   => ['nullable', 'integer', 'min:0'],
            'intake_iv_ml'     => ['nullable', 'integer', 'min:0'],
            'intake_tube_ml'   => ['nullable', 'integer', 'min:0'],

            'output_urine_ml'  => ['nullable', 'integer', 'min:0'],
            'output_stool_ml'  => ['nullable', 'integer', 'min:0'],
            'output_emesis_ml' => ['nullable', 'integer', 'min:0'],
            'output_drain_ml'  => ['nullable', 'integer', 'min:0'],

            'remarks' => ['nullable', 'string', 'max:2000'],

            /* ------------------------------
             | Lifecycle fields (optional)
             * ------------------------------ */
            'status'         => ['nullable', 'in:active,discharged'],
            'discharged_at'  => ['nullable', 'date'],
            'archived_at'    => ['nullable', 'date'],
            'archive_reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'ended_at.after_or_equal' => 'The end time must be after or equal to the start time.',
            'status.in'               => 'Status must be either Active or Discharged.',
        ];
    }
}