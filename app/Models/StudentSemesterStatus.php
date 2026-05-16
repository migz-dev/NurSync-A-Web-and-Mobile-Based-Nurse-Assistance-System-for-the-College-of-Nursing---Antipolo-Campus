<?php

// app/Models/StudentSemesterStatus.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentSemesterStatus extends Model
{
    protected $table = 'student_semester_statuses';
    public $timestamps = true;
    protected $fillable = [
        'student_id','term_id','status','regcard_file_id',
        'submitted_at','validated_at','expires_at','review_note','reviewed_by'
    ];
}
