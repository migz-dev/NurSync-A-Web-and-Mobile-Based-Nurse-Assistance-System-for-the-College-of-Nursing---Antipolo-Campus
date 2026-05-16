<?php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNursesNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('faculty')->check();
    }

    public function rules(): array
    {
        $formats = ['narrative', 'dar', 'soapie', 'pie'];

        return [
            // Header / context
            'patient_name' => ['nullable', 'string', 'max:120'],     // free-text label (like Vital Signs)
            'encounter_id' => ['nullable', 'integer'],               // keep optional; no "exists" required if field is hidden
            'noted_at'     => ['required', 'date'],
            'format'       => ['required', Rule::in($formats)],

            // Narrative
            'narrative'    => ['nullable', 'string', 'required_if:format,narrative'],

            // DAR
            'dar_data'     => ['nullable', 'string', 'required_if:format,dar'],
            'dar_action'   => ['nullable', 'string', 'required_if:format,dar'],
            'dar_response' => ['nullable', 'string', 'required_if:format,dar'],

            // SOAPIE
            'soapie_s'     => ['nullable', 'string', 'required_if:format,soapie'],
            'soapie_o'     => ['nullable', 'string', 'required_if:format,soapie'],
            'soapie_a'     => ['nullable', 'string', 'required_if:format,soapie'],
            'soapie_p'     => ['nullable', 'string', 'required_if:format,soapie'],
            'soapie_i'     => ['nullable', 'string', 'required_if:format,soapie'],
            'soapie_e'     => ['nullable', 'string', 'required_if:format,soapie'],

            // PIE
            'pie_p'        => ['nullable', 'string', 'required_if:format,pie'],
            'pie_i'        => ['nullable', 'string', 'required_if:format,pie'],
            'pie_e'        => ['nullable', 'string', 'required_if:format,pie'],

            // Misc
            'remarks'      => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        // Friendlier messages for the conditional blocks
        return [
            'narrative.required_if' => 'The narrative field is required when format is narrative.',

            'dar_data.required_if'     => 'The DAR "Data" field is required when format is DAR.',
            'dar_action.required_if'   => 'The DAR "Action" field is required when format is DAR.',
            'dar_response.required_if' => 'The DAR "Response" field is required when format is DAR.',

            'soapie_s.required_if' => 'The SOAPIE S (Subjective) field is required when format is SOAPIE.',
            'soapie_o.required_if' => 'The SOAPIE O (Objective) field is required when format is SOAPIE.',
            'soapie_a.required_if' => 'The SOAPIE A (Assessment) field is required when format is SOAPIE.',
            'soapie_p.required_if' => 'The SOAPIE P (Plan) field is required when format is SOAPIE.',
            'soapie_i.required_if' => 'The SOAPIE I (Intervention) field is required when format is SOAPIE.',
            'soapie_e.required_if' => 'The SOAPIE E (Evaluation) field is required when format is SOAPIE.',

            'pie_p.required_if' => 'The PIE "Problem" field is required when format is PIE.',
            'pie_i.required_if' => 'The PIE "Intervention" field is required when format is PIE.',
            'pie_e.required_if' => 'The PIE "Evaluation" field is required when format is PIE.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalize empty strings to null and default format if missing (defensive)
        $payload = collect($this->all())->map(function ($v) {
            return ($v === '') ? null : $v;
        })->all();

        if (!isset($payload['format']) || $payload['format'] === null) {
            $payload['format'] = 'narrative';
        }

        $this->merge($payload);
    }
}