<?php

namespace App\Eloquent;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CustomerFilter
 * @package App\Eloquent
 * @property integer $id
 * @property string $filter_url
 * @property string $filter_title
 * @property string $spot_type
 * @property int $customer_id
 * @property string $title
 * @property boolean $enabled
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $schedule
 * @property Customer $customer
 * @property CustomerItem $items
 */
class CustomerFilter extends Model
{

    public const SCHEDULERS = ['1 day', '1 week', '1 hour', '6 hours', '12 hours'];
    public const SPOT_IAAI = 'www.iaai.com';
    public const SPOT_OLX = 'www.olx.ua';
    public const SPOT_AUTORIA = 'auto.ria.com';

    protected $table = 'customer_filter';
    protected $fillable = ['filter_url', 'filter_title', 'spot_type', 'title', 'enabled', 'schedule', 'user_id'];

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(CustomerItem::class, 'filter_id', 'id');
    }
}
