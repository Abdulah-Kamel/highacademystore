<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialVideo extends Model
{
    use HasFactory;
    protected $fillable = ['title','image','video','chapter_id'];
    public function chapters ()
    {
        return $this->belongsTo(Chapter::class,'chapter_id');
    }
}
