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
 * @property string $foreign_product_id
 * @property string $last_sync_date
 * @property int $category_id
 * @property Category $category
 * @property PriceOption[] $priceOptions
 * @property PriceOption[] $syncPriceOptions
 * @property Image[] $images
 * @property string $created_at
 * @property string $updated_at
 */
class Product extends Model
{
    protected $table = 'product';
    protected $fillable = [
        'name',
        'description',
        'url',
        'image_url',
        'price',
        'article',
        'foreign_product_id',
        'last_sync_date',
        'active'
    ];

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
    public function syncPriceOptions(): HasMany
    {
        return $this->hasMany(PriceOption::class)->where('foreign_option_id', '=!', null);
    }

    public function updateLastSync($foreignId = null)
    {
        if ($foreignId) {
            $this->foreign_product_id = $foreignId;
        }
        $this->last_sync_date = date('Y-m-d h:i:s');
        $this->save();
    }

    /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}
