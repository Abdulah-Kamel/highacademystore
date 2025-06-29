<?php

namespace App\Models;


// use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Brand extends Model implements TranslatableContract
{
    // use HasFactory;

    use Translatable;

    protected $table = 'brands';

    public $translatedAttributes = ['title','description'];
    protected $fillable = ['photo'];

    public $translationModel = BrandTranslation::class;

    public function getImagePathAttribute()
    {
        return asset('storage/images/brands/'. $this->photo);
    } //end of image path attribute

    public function products()
    {
        return $this->hasMany(Product::class);
    }//end of products
}
