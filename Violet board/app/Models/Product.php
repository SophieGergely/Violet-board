<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'discounted_price',
        'is_discounted',
        'is_best_seller',
        'is_new',
        'min_age',
        'min_players',
        'max_players',
        'in_stock',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }


    public function getMainImagePath()
    {
        return asset('Pictures/' . Str::slug($this->name) . '1.jpg');
    }


    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function scopeDiscounted($query)
    {
        return $query->whereNotNull('discounted_price');
    }

}
