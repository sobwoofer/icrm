<?php

namespace App\Eloquent\Product;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property int $vendor_id
 * @property int $parent_id
 * @property Vendor $vendor
 * @property Category $parent
 * @property Product[] $products
 * @property string $created_at
 * @property string $updated_at
 */
class Category extends Model
{
    protected $table = 'category';
    protected $fillable = ['name', 'url', 'vendor_id', 'parent_id'];

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
