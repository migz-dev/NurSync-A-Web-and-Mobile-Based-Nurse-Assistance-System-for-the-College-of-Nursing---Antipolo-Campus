<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FacultyRegisterController extends Controller
{
    public function store(Request $req)
    {
        // Same options as your dropdown in the Blade
        $nurseTypeOptions = [
            'Nurse Practitioner',
            'Emergency room nurse',
            'Oncology nursing',
            'Labor and Delivery Nurse',
            'Licensed Practical Nurse',
            'Nurse Anesthetist',
            'Cardiac nurse',
            'Clinical nurse specialist',
            'Home Health nurse',
            'Nurse educator',
            'Nurse midwife',
            'Critical care nursing',
            'ICU nurse',
            'Mental health nursing',
            'Pediatric nursing',
            'Surgical nurses',
            'Travel Nurse',
            'Informatics nurse',
            'Public Health Nurse',
            'Geriatric nurse',
            'NICU nurse',
            'Nurse administrator',
            'Operating Room nurse',
        ];

        $data = $req->validate([
            'full_name'      => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', Rule::unique('faculty', 'email')],
            'faculty_id'     => ['required', 'string', 'max:50', Rule::unique('faculty', 'faculty_id')],
            'nurse_type'     => ['required', 'string', 'max:255', Rule::in($nurseTypeOptions)],
            'password'       => ['required', 'confirmed', 'min:8'],
            'faculty_id_file'=> ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:8192'],
        ]);

        $path = $req->file('faculty_id_file')->storeAs(
            'faculty_ids',
            now()->format('Ymd_His') . '_' . Str::slug($data['faculty_id']) . '.' . $req->file('faculty_id_file')->getClientOriginalExtension(),
            'public'
        );

        DB::table('faculty')->insert([
            'full_name'   => $data['full_name'],
            'email'       => $data['email'],
            'faculty_id'  => $data['faculty_id'],
            'nurse_type'  => $data['nurse_type'],
            'password'    => Hash::make($data['password']),
            'id_file_path'=> 'storage/' . $path, // publicly accessible via storage:link
            'status'      => 'pending',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()
            ->route('faculty.login')
            ->with('status', 'Your account is pending admin verification. You’ll be notified once approved.');
    }
}
