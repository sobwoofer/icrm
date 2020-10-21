<?php

namespace App\Eloquent;

use App\Eloquent\Product\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class VendorToClient
 * @package App\Eloquent
 * @property integer $id
 * @property integer $vendor_id
 * @property integer $client_site_id
 * @property boolean $active
 * @property Vendor $vendor
 * @property ClientSite $clientSite
 * @property string $created_at
 * @property string $updated_at
 */
class VendorToClient extends Model
{
    protected $table = 'vendor_to_client';
    protected $fillable = ['vendor_id', 'client_site_id', 'active'];

    /**
     * @return BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(BelongsTo::class, 'id', 'vendor_id');
    }

    /**
     * @return BelongsTo
     */
    public function clientSite(): BelongsTo
    {
        return $this->belongsTo(BelongsTo::class, 'id', 'client_site_id');
    }

}
