<?php

namespace App\Eloquent;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property string $phone
 * @property string $chat_id
 * @property string $username
 * @property string $state
 * @property string $first_name
 * @property string $last_name
 * @property integer $update_id
 * @property integer $user_id
 * @property User $user
 * @property CustomerFilter[] $filters
 * @property CustomerCred[] $creds
 * @property CustomerCred $cred
 * @property string $created_at
 * @property string $updated_at
 */
class Customer extends Model
{
    public const STATE_START = 'start';
    public const STATE_ADD_FILTER = 'addFilter';
    public const STATE_ADD_FILTER_TITLE = 'addFilterTitle';
    public const STATE_ADD_FILTER_SCHEDULE = 'addFilterSchedule';
    public const STATE_HUNTING = 'hunting';
    public const STATE_REMOVE_FILTER = 'removeFilter';
    public const STATE_SHOW_FILTERS = 'showFilters';

    protected $table = 'customer';
    protected $fillable = ['phone', 'chat_id', 'state', 'username', 'update_id', 'first_name', 'last_name', 'user_id'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function filters(): HasMany
    {
        return $this->hasMany(CustomerFilter::class);
    }

    /**
     * @return HasMany
     */
    public function creds(): HasMany
    {
        return $this->hasMany(CustomerCred::class);
    }

    /**
     * @param string $state
     * @return bool
     */
    public function setState(string $state): bool
    {
        $this->state = $state;
        return $this->save();
    }

    public function setUpdateId(string $updateId): bool
    {
        $this->update_id = $updateId;
        return $this->save();
    }

//    public function scopeCred(Builder $query, string $spotType): Builder
//    {
//        return $query->where('spot_type', '=', $spotType);
//    }

}
