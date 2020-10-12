<?php

namespace App\Sharp\Filters;

use App\Eloquent\Product\Vendor;
use Code16\Sharp\EntityList\EntityListSelectFilter;

class VendorFilter implements EntityListSelectFilter
{
    /**
     * @return array
     */
    public function values()
    {
        return Vendor::orderBy('id')->pluck('name', 'id')->all();
    }
}
