<?php

namespace App\Eloquent;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property string $spot_type
 * @property string $description
 * @property int $customer_id
 * @property Customer $customer
 * @property User $user
 * @property string $created_at
 * @property string $updated_at
 */
class CustomerCred extends Model
{
    protected $table = 'customer_cred';
    protected $fillable = ['login', 'password', 'spot_type', 'description', 'customer_id'];

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeSpotType(Builder $query, int $spotType): Builder
    {
        return $query->where('spot_type', $spotType);
    }
}
