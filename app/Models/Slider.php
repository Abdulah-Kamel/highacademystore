<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model implements TranslatableContract
{
    // use HasFactory;

    use Translatable;

    protected $table = 'sliders';

    public $translatedAttributes = ['title', 'description'];
    protected $fillable = ['stage_id', 'photo', 'is_active'];

    public $translationModel = SliderTranslation::class;

    public function scopeActived($query)
    {
        return $query->where('is_active', 2);
    }

    public function getImagePathAttribute()
    {
        return asset('storage/images/sliders/' . $this->photo);
    } //end of image path attribute

    public function stage()
    {
        return $this->belongsTo(Stage::class, "stage_id");
    }
}
