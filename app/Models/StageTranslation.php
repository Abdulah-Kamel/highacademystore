<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageTranslation extends Model
{
    // use HasFactory;
    public $timestamps = false;

    protected $table = 'stages_translations';

    protected $fillable = ['title', 'description'];
}
