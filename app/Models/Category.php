<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Support\Facades\DB;

class Category extends Model implements TranslatableContract
{
    // use HasFactory;

    use Translatable;

    protected $table = 'categories';

    public $translatedAttributes = ['title'];
    protected $fillable = ['photo','parent_id','is_parent'];

    public $translationModel = CategoryTranslation::class;

    protected $appends = ['image_path'];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getImagePathAttribute()
    {
        return asset('storage/images/categories/'. $this->photo);
    } //end of image path attribute

    public function products()
    {
        return $this->hasMany(Product::class);
    }//end of products

    public static function getChildByParentID($id)
    {
        $categories = DB::table('category_translations')
            ->join('categories', 'categories.id', '=', 'category_translations.category_id')
            ->select('categories.id', "category_translations.title as title")
            ->where('categories.parent_id', $id)
            ->pluck('title', 'id');

        return $categories;
    }
}
