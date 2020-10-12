<?php

namespace App\Sharp\Filters;

use App\Eloquent\Product\Category;
use Code16\Sharp\EntityList\EntityListSelectFilter;

class CategoryFilter implements EntityListSelectFilter
{
    /**
     * @return array
     */
    public function values()
    {
        return Category::orderBy('id')->pluck('name', 'id')->all();
    }
}
