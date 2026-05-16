<?php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarRequest extends FormRequest
{
    public function authorize(): bool { return auth('faculty')->check(); }

    public function rules(): array
    {
        return [
            'taken_at'       => ['nullable','date'], // (if you later add a taken field)
            'medication'     => ['required','string','max:160'],
            'dose_amount'    => ['nullable','numeric','min:0','max:999999'],
            'dose_unit'      => ['nullable','string','max:16'],
            'route'          => ['nullable','string','max:32'],
            'frequency'      => ['nullable','string','max:32'],
            'is_prn'         => ['sometimes','boolean'],
            'prn_reason'     => ['nullable','string','max:160'],
            'administered_at'=> ['nullable','date'],
            'omitted_reason' => ['nullable','string','max:160'],
            'effects'        => ['nullable','string','max:5000'],
            'remarks'        => ['nullable','string','max:2000'],
        ];
    }
}