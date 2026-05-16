<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimAssignmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Handled in controller via policies; still require logged-in faculty
        return auth('faculty')->check();
    }

    public function rules(): array
    {
        return [
            'case_id'                 => 'sometimes|integer|exists:sim_cases,id',
            'title'                   => 'sometimes|string|max:255',
            'instructions'            => 'nullable|string',
            'due_at'                  => 'nullable|date',
            'required_modules_json'     => 'nullable|array',
            'required_modules_json.*'   => 'in:vitals,mar,io,nn,ncp,treatment',
            'visibility'              => 'sometimes|in:class,individual',
            'assignee_student_ids'    => 'sometimes|array',
            'assignee_student_ids.*'  => 'integer|exists:students,id',
        ];
    }
}