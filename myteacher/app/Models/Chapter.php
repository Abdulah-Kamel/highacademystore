<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public function materialsPDF()
    {

    }
    public function materialsVideos()
    {
        return $this->hasMany(MaterialVideo::class,'chapter_id');

    }
}
