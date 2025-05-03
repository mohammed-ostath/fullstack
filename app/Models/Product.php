<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'sku',
        'is_active',
    ];

    public function inStock(){
        return $this->stock > 0;
    }

    protected static function booted(){
        static::addGlobalScope('active', function ($query) {
            $query->where('is_active', true);
        });
    }

    public function scopePriceBetween($query, $min, $max)
    {
        return $query->where('price', [$min, $max]);
    }

    public function getFormattedNameAttribute()
    {
        return ucwords($this->name);
    }

}
