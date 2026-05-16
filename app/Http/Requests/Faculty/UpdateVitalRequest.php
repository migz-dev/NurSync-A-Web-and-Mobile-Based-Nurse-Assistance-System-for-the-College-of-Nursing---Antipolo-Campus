<?php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVitalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('faculty')->check();
    }

    public function rules(): array
    {
        return [
            'taken_at'     => ['sometimes','date'],
            'temp_c'       => ['nullable','numeric','between:30,45'],
            'pulse_bpm'    => ['nullable','integer','between:20,220'],
            'resp_rate'    => ['nullable','integer','between:5,80'],
            'bp_systolic'  => ['nullable','integer','between:50,260'],
            'bp_diastolic' => ['nullable','integer','between:20,180'],
            'spo2'         => ['nullable','integer','between:0,100'],
            'pain_scale'   => ['nullable','integer','between:0,10'],
            'remarks'      => ['nullable','string','max:500'],
        ];
    }
}