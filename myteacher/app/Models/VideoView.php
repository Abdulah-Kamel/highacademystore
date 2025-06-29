<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoView extends Model
{
    use HasFactory;

    protected $fillable = ['material_video_id', 'student_id'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'video_views');
    }
    public function material_video()
    {
        return $this->belongsTo(MaterialVideo::class, 'material_video_id');
    }
}
