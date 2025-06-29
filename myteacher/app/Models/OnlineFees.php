<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineFees extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'chapter_id', 'price', 'image', 'status'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'online_fees');
    }

    public function chapters()
    {
        return $this->belongsToMany(Chapter::class, 'online_fees');
    }
}
