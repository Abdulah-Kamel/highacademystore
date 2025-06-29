<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'degree', 'grade_id', 'date'];

    public function grades()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'exams_students');
    }
}
