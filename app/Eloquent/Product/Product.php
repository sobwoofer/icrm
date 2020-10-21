<?php

namespace App\Eloquent\Product;

use App\Eloquent\ClientSite;
use App\Eloquent\ProductToClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property ProductToClient[] $productToClients
 * @property ClientSite[] $clientSites
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
        'active'
    ];

    /**
     * @return HasMany
     */
    public function productToClients(): HasMany
    {
        return $this->hasMany(ProductToClient::class,'product_id', 'id');
    }

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
     * @return BelongsToMany
     */
    public function clientSites(): BelongsToMany
    {
        return $this->belongsToMany(ClientSite::class, 'product_to_client');
    }

    /**
     * @return HasMany
     */
    public function syncPriceOptions(): HasMany
    {
        return $this->hasMany(PriceOption::class)
            ->with('foreignOption')
            ->where('foreign_option_id', '!=', null);
    }

    /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}
