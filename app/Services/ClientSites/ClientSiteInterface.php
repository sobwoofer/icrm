<?php

namespace App\Services\ClientSites;

use App\Eloquent\Product\Product;
use App\Eloquent\ProductToClient;

interface ClientSiteInterface
{
    public function createProduct(Product $product): ?string;

    public function updateProduct(Product $product, ProductToClient $productToClient): ?string;

}
