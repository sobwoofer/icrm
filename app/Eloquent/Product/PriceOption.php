<?php

namespace App\Eloquent\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property string $name
 * @property int $product_id
 * @property float $price
 * @property Product $product
 * @property string $created_at
 * @property string $updated_at
 */
class PriceOption extends Model
{
    protected $table = 'price_option';
    protected $fillable = ['name', 'price', 'product_id'];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
