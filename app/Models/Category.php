<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'parent_id',
    ];
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function activeChildren(){
        return $this->children()->where('is_active', true);
    }

    public function isTopLevel(){
        return is_null($this->parent_id);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

}
