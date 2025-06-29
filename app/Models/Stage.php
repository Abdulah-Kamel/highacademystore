<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StageTranslation;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Stage extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'stages';

    public $translatedAttributes = ['title', 'description'];
    protected $fillable = ['photo', 'is_active'];

    public $translationModel = StageTranslation::class;

    public function scopeActived($query)
    {
        return $query->where('is_active', 2);
    }

    public function getImagePathAttribute()
    {
        return asset('storage/images/stages/' . $this->photo);
    } //end of image path attribute
}
