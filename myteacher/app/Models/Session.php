<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_from', 'start_to', 'grade_id'];

    public function grades()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'sessions_students');
    }

    public function days()
    {
        return $this->hasMany(SessionDays::class, 'session_id');
    }

    public function attedance()
    {
        return $this->belongsToMany(Student::class, 'attendance');
    }
}
