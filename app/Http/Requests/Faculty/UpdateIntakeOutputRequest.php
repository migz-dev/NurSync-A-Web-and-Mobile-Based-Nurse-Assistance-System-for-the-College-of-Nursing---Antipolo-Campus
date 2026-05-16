<?php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIntakeOutputRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only logged-in faculty can update Intake & Output
        return auth('faculty')->check();
    }

    public function rules(): array
    {
        return [
            // Time window
            'started_at' => ['nullable', 'date'],
            'ended_at'   => ['nullable', 'date', 'after_or_equal:started_at'],

            // Intake fields (ml)
            'intake_oral_ml'  => ['nullable', 'integer', 'min:0'],
            'intake_iv_ml'    => ['nullable', 'integer', 'min:0'],
            'intake_tube_ml'  => ['nullable', 'integer', 'min:0'],

            // Output fields (ml)
            'output_urine_ml'  => ['nullable', 'integer', 'min:0'],
            'output_stool_ml'  => ['nullable', 'integer', 'min:0'],
            'output_emesis_ml' => ['nullable', 'integer', 'min:0'],
            'output_drain_ml'  => ['nullable', 'integer', 'min:0'],

            // Remarks
            'remarks' => ['nullable', 'string', 'max:2000'],

            // Lifecycle management
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