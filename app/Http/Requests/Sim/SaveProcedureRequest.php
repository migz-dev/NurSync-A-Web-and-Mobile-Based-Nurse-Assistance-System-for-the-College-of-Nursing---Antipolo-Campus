<?php

namespace App\Http\Requests\Sim;

use Illuminate\Foundation\Http\FormRequest;

class SaveProcedureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'procedure_name' => ['required','string','max:191'],
            'details'        => ['nullable','array'],
            'performed_at'   => ['nullable','date'],
        ];
    }
}