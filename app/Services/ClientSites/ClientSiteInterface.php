<?php

namespace App\Services\ClientSites;

use App\Eloquent\Product\Product;

interface ClientSiteInterface
{
    public function createProduct(Product $product): ?string;

    public function updateProduct(Product $product): ?string;

}
