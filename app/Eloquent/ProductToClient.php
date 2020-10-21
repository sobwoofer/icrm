<?php

namespace App\Eloquent;

use App\Eloquent\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class ClientSite
 * @package App\Eloquent
 * @property integer $id
 * @property integer $product_id
 * @property integer $client_site_id
 * @property integer $client_product_id
 * @property boolean $active
 * @property Product $product
 * @property ClientSite $clientSite
 * @property string $created_at
 * @property string $updated_at
 */
class ProductToClient extends Model
{
    protected $table = 'product_to_client';
    protected $fillable = ['product_id', 'client_site_id', 'client_product_id', 'active'];


    /**
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    /**
     * @return HasOne
     */
    public function clientSite(): HasOne
    {
        return $this->hasOne(ClientSite::class, 'id', 'client_site_id');
    }

}
