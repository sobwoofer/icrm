<?php

namespace App\Eloquent\Product;

use App\Eloquent\ClientSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $site_url
 * @property Category[] $categories
 * @property ClientSite[] $clientSites
 * @property string $created_at
 * @property string $updated_at
 */
class Vendor extends Model
{
    public const SLUG_COMEFOR = 'comefor';
    public const SLUG_EMM = 'emm';
    public const SLUG_MATROLUX = 'matrolux';
    public const SLUG_DIPLOMAT = 'diplomat';

    protected $table = 'vendor';
    protected $fillable = ['name', 'slug', 'site_url'];

    /**
     * @return HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * @return BelongsToMany
     */
    public function clientSites(): BelongsToMany
    {
        return $this->belongsToMany(ClientSite::class, 'vendor_to_client');
    }

}
