<?php

namespace App\Eloquent;

use App\Eloquent\Product\Product;
use App\Eloquent\Product\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class ClientSite
 * @package App\Eloquent
 * @property integer $id
 * @property string $url
 * @property string $type
 * @property string $auth_key
 * @property string $slug
 * @property Vendor[] $vendors
 * @property boolean $active
 * @property string $created_at
 * @property string $updated_at
 */
class ClientSite extends Model
{
    public const TYPE_OPENCART = 'opencart';
    public const TYPE_OTHER = 'other';

    protected $table = 'client_site';
    protected $fillable = ['slug', 'active', 'url', 'type', 'auth_key'];

    /**
     * @return BelongsToMany
     */
    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'vendor_to_client');
    }

    public function assignProduct(Product $product, string $foreignProductId)
    {
        $productToClient = new ProductToClient();
        $productToClient->product_id = $product->id;
        $productToClient->client_site_id = $this->id;
        $productToClient->client_product_id = $foreignProductId;
        $productToClient->save();
    }

    public function updateLastSync(Product $product)
    {
        $productToClient = ProductToClient::query()
            ->where('product_id', $product->id)
            ->where('client_site_id', $this->id)
            ->first();
        $productToClient->setUpdatedAt(date('Y-m-d H:i:s'))->save();
    }

    public function getVendorIds(): array
    {
        $ids = [];
        foreach ($this->vendors as $vendor) {
            $ids[] = $vendor->id;
        }
        return $ids;
    }
}
