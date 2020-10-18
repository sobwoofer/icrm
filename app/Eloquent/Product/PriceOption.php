<?php

namespace App\Eloquent\Product;

use App\Eloquent\ForeignOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property string $name
 * @property int $product_id
 * @property float $price
 * @property string $foreign_option_id
 * @property Product $product
 * @property ForeignOption $foreignOption
 * @property string $created_at
 * @property string $updated_at
 */
class PriceOption extends Model
{
    protected $table = 'price_option';
    protected $fillable = ['name', 'price', 'product_id', 'foreign_id'];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function foreignOption(): HasOne
    {
        return $this->hasOne(ForeignOption::class, 'id', 'foreign_option_id');
    }

    public function save(array $options = [])
    {
        $savingResult = parent::save($options);

        $this->product->setUpdatedAt(date('Y-m-d H:i:s'))->save();

        return $savingResult;
    }
}
