<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimAssignmentStoreRequest extends FormRequest
{
    public function authorize(): bool { return auth('faculty')->check(); }

    public function rules(): array
    {
        return [
            'case_id'                 => 'required|integer|exists:sim_cases,id',
            'title'                   => 'required|string|max:255',
            'instructions'            => 'nullable|string',
            'due_at'                  => 'nullable|date',
            'required_modules_json'     => 'nullable|array',
            'required_modules_json.*'   => 'in:vitals,mar,io,nn,ncp,treatment',
            'visibility'              => 'required|in:class,individual',
            'assignee_student_ids'    => 'nullable|array',
            'assignee_student_ids.*'  => 'integer|exists:students,id',
        ];
    }
}
