<?php

namespace App\Sharp\Entities\Vendor;

use App\Eloquent\Product\Vendor;
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
            ->setSearchable()
            ->setDefaultSort('name', 'asc')
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
        return $this->transform(Vendor::all());
    }
}
