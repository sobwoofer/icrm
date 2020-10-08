<?php

namespace App\Eloquent\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $url
 * @property string $image_url
 * @property float $price
 * @property string $article
 * @property Category $category
 * @property PriceOption[] $priceOptions
 * @property Image[] $images
 * @property string $created_at
 * @property string $updated_at
 */
class Product extends Model
{
    protected $table = 'product';
    protected $fillable = ['name', 'description', 'url', 'image_url', 'price', 'article'];

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany
     */
    public function priceOptions(): HasMany
    {
        return $this->hasMany(PriceOption::class);
    }

    /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}
