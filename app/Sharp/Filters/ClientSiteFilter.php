<?php

namespace App\Sharp\Filters;

use App\Eloquent\ClientSite;
use Code16\Sharp\EntityList\EntityListSelectFilter;

class ClientSiteFilter implements EntityListSelectFilter
{
    /**
     * @return array
     */
    public function values()
    {
        return ClientSite::orderBy('id')->pluck('slug', 'id')->all();
    }
}
