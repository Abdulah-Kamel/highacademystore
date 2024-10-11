<?php

namespace App\Models;

use App\Enums\ProductEnum;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Product extends Model implements TranslatableContract
{
    // use HasFactory;

    use Translatable;

    protected $table = 'products';

    public $translatedAttributes = ['name', 'description'];
    protected $fillable = [
        'photo',
        'price',
        'have_offer',
        'slider_id',
        'offer_type',
        'offer_value',
        'rate',
        'rate_count',
        'brand_id',
        'category_id',
        'child_cat_id',
        'final_price',
        "state"
    ];
    protected $casts = [
        'name' => 'json',
        'description' => 'json',
    ];

    public $translationModel = ProductTranslation::class;

    protected $appends = ['ProfitPercent'];

    public function getImagePathAttribute()
    {
        return asset('storage/images/products/' . $this->photo);
    } //end of image path attribute

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }//end of category

    public function sliders()
    {
        return $this->belongsTo(Slider::class, 'slider_id');
    }//end of category

    public function sub_cat_info()
    {
        return $this->belongsTo(Category::class, 'child_cat_id');
    }//end of sub category

    public function brands()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }//end of Brand

    public function getProfitPercentAttribute()
    {
        if ($this->offer_type == 'percentage') {
            $offer_price = $this->price - ($this->offer_value * $this->price / 100);
            return number_format($offer_price, 2);
        } else {
            $offer_price = $this->price - $this->offer_value;
            return number_format($offer_price, 2);
        }
    } // end of get profit attribute


    /* getProductByCart */
    public static function getProductByCart($id)
    {
        return self::where('id', $id)->get()->toArray();
    }
}
