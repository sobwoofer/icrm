<?php

namespace App\Eloquent\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property string $url
 * @property string $product_id
 * @property Product $product
 * @property string $created_at
 * @property string $updated_at
 */
class Image extends Model
{
    protected $table = 'image';
    protected $fillable = ['url', 'product_id'];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
