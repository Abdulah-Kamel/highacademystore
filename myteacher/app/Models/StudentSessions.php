<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSessions extends Model
{
    use HasFactory;

    protected $table = 'sessions_students';
    protected $fillable = ['session_id', 'student_id'];
}
