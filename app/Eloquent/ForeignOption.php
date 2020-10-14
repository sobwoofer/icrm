<?php

namespace App\Eloquent;

use App\Eloquent\Product\PriceOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property int $created
 * @property int $updated
 * @property int $name
 * @property int $foreign_option_id
 * @property PriceOption[] $priceOptions
 * @property string $created_at
 * @property string $updated_at
 */
class ForeignOption extends Model
{
    protected $table = 'foreign_option';
    protected $fillable = ['foreign_option_id', 'name'];

    /**
     * @return HasMany
     */
    public function priceOptions(): HasMany
    {
        return $this->hasMany(PriceOption::class, 'foreign_id', 'id');
    }

}
