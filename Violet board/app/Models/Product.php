<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'label_id',
        'discount_id',
        'name',
        'description',
        'price',
        'min_age',
        'min_players',
        'max_players',
        'in_stock',
        'play_time_min',
        'play_time_max',
        'bgg_rating',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'price'      => 'decimal:2',
            'in_stock'   => 'boolean',
            'bgg_rating' => 'decimal:2',
            'weight'     => 'decimal:2',
            'deleted_at' => 'datetime',
        ];
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_products')
                    ->withPivot('created_at');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorites')
                    ->withPivot('created_at');
    }

    public function effectivePrice(): float
    {
        if ($this->discount && $this->discount->isActive()) {
            return $this->discount->calculatePrice((float) $this->price);
        }

        return (float) $this->price;
    }
}
