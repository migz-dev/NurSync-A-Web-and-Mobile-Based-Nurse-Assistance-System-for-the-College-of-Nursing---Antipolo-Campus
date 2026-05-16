<?php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarRequest extends FormRequest
{
    public function authorize(): bool { return auth('faculty')->check(); }

    public function rules(): array
    {
        return [
            // Quick patient & encounter (UI lets CI type these in)
            'quick_patient.last_name'        => ['required','string','max:100'],
            'quick_patient.first_name'       => ['required','string','max:100'],
            'quick_patient.hospital_no'      => ['nullable','string','max:50'],
            'quick_encounter.unit'           => ['required','string','max:120'],
            'quick_encounter.started_at'     => ['required','date'],
            'quick_encounter.remarks'        => ['nullable','string','max:1000'],

            // MAR fields
            'medication'                     => ['required','string','max:160'],
            'dose_amount'                    => ['nullable','numeric','min:0','max:999999'],
            'dose_unit'                      => ['nullable','string','max:16'],
            'route'                          => ['nullable','string','max:32'],
            'frequency'                      => ['nullable','string','max:32'],
            'is_prn'                         => ['sometimes','boolean'],
            'prn_reason'                     => ['nullable','string','max:160'],
            'administered_at'                => ['nullable','date'],
            'omitted_reason'                 => ['nullable','string','max:160'],
            'effects'                        => ['nullable','string','max:5000'],
            'remarks'                        => ['nullable','string','max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'quick_patient.last_name.required'    => 'Patient last name is required.',
            'quick_patient.first_name.required'   => 'Patient first name is required.',
            'quick_encounter.unit.required'       => 'Unit/Area is required.',
            'quick_encounter.started_at.required' => 'Encounter start time is required.',
            'medication.required'                 => 'Medication name is required.',
        ];
    }
}
