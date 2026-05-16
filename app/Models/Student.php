<?php

// app/Models/Student.php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    protected $table = 'students';
    // If you’re not using remember_token/etc, keep as is.
    protected $fillable = ['name','email','student_number'];
}
