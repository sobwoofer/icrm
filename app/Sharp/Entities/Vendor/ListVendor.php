<?php

namespace App\Sharp\Entities\Vendor;

use App\Eloquent\Product\Vendor;
use App\Sharp\Filters\ClientSiteFilter;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

class ListVendor extends SharpEntityList
{
    /**
    * Build list containers using ->addDataContainer()
    *
    * @return void
    */
    public function buildListDataContainers()
    {
        $this->addDataContainer(
            EntityListDataContainer::make('id')
                ->setLabel('id')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('name')
                ->setLabel('name')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('slug')
                ->setLabel('slug')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('site_url')
                ->setLabel('site_url')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('created_at')
                ->setLabel('created_at')
                ->setSortable()
        );
    }

    /**
    * Build list layout using ->addColumn()
    *
    * @return void
    */

    public function buildListLayout()
    {
        $this->addColumn('id', 1);
        $this->addColumn('name', 2);
        $this->addColumn('slug', 2);
        $this->addColumn('site_url', 2);
        $this->addColumn('created_at', 2);
    }

    /**
    * Build list config
    *
    * @return void
    */
    public function buildListConfig()
    {
        $this->setInstanceIdAttribute('id')
            ->setDefaultSort('name', 'asc')
            ->addFilter('client_site', ClientSiteFilter::class)
            ->setSearchable()
            ->setPaginated();
    }

    /**
    * Retrieve all rows data as array.
    *
    * @param EntityListQueryParams $params
    * @return array
    */
    public function getListData(EntityListQueryParams $params)
    {
        $item = Vendor::query();

        if ($clientSite = $params->filterFor('client_site')) {
            $item->leftJoin('vendor_to_client', 'vendor.id', '=', 'vendor_to_client.vendor_id')
                ->leftJoin('client_site', 'client_site.id', '=', 'vendor_to_client.client_site_id')
                ->where('client_site.id', $clientSite);
        }

        return $this->transform($item->paginate(30, ['vendor.*']));
    }
}
