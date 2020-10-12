<?php

namespace App\Eloquent;

use App\Eloquent\Product\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property int $created
 * @property int $updated
 * @property int $crawled
 * @property int $vendor_id
 * @property Vendor $vendor
 * @property string $created_at
 * @property string $updated_at
 */
class CrawlStat extends Model
{
    protected $table = 'crawl_stat';
    protected $fillable = ['crawled', 'created', 'updated', 'vendor_id'];

    /**
     * @return BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * @param int|null $vendorId
     * @return static
     */
    public static function create(int $vendorId = null): self
    {
        $model = new static();
        $model->vendor_id = $vendorId;
        $model->save();
        return $model;
    }

    public function incrUpdated(int $incrValue = 1)
    {
        $this->updated += $incrValue;
        $this->save();
    }

    public function incrCreated(int $incrValue = 1)
    {
        $this->created += $incrValue;
        $this->save();
    }

    public function incrCrawled(int $incrValue = 1)
    {
        $this->created += $incrValue;
        $this->save();
    }

}
