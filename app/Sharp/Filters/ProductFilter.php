<?php

namespace App\Sharp\Filters;

use App\Eloquent\Product\Product;
use Code16\Sharp\EntityList\EntityListSelectFilter;

class ProductFilter implements EntityListSelectFilter
{
    /**
     * @return array
     */
    public function values()
    {
        return Product::orderBy('id')->pluck('name', 'id')->all();
    }
}
