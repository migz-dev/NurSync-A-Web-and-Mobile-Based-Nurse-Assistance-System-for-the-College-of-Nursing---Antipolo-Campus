<?php
// app/Models/AcademicTerm.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    protected $table = 'academic_terms';
    public $timestamps = true; // set false if your table lacks timestamps
    protected $fillable = ['code','name','semester','is_current','start_date','end_date'];
}

