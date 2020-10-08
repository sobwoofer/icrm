<?php

namespace App\Eloquent;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CustomerFilter
 * @package App\Eloquent
 * @property string $url
 * @property string $title
 * @property string $description
 * @property string $image
 * @property integer $filter_id
 * @property string $created_at
 * @property string $updated_at
 * @property CustomerFilter $filter
 * @method betweenDatesByFilterId(int $filterId, Carbon $dateFrom, Carbon $dateTo): Builder
 */
class CustomerItem extends Model
{
    protected $table = 'customer_item';
    protected $fillable = ['url', 'title', 'description', 'image', 'filter_id'];

    /**
     * @return BelongsTo
     */
    public function filter(): BelongsTo
    {
        return $this->belongsTo(CustomerFilter::class);
    }

    /**
     * @param Builder $query
     * @param int $filterId
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     * @return Builder
     */
    public function scopeBetweenDatesByFilterId(
        Builder $query,
        int $filterId,
        Carbon $dateFrom,
        Carbon $dateTo
    ): Builder
    {
        return $query
            ->whereBetween('created_at', [$dateFrom->toDateTimeString(), $dateTo->toDateTimeString()])
            ->where('filter_id', $filterId);
    }

}
