<?php

namespace App\Sharp\Entities\Product;

use App\Eloquent\Product\Product;
use App\Sharp\Filters\VendorFilter;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

class ListProduct extends SharpEntityList
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
                ->setLabel('Id')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('name')
                ->setLabel('name')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('url')
                ->setLabel('url')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('price')
                ->setLabel('price')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('foreign_product_id')
                ->setLabel('foreign_product_id')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('created_at')
                ->setLabel('created_at')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('updated_at')
                ->setLabel('updated_at')
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
        $this->addColumn('id', 1)
        ->addColumn('name', 2)
        ->addColumn('url', 2)
        ->addColumn('price', 1)
        ->addColumn('foreign_product_id', 2)
        ->addColumn('created_at', 2)
        ->addColumn('updated_at', 2);
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
            ->setDefaultSort('id', 'asc')
            ->addFilter('vendor', VendorFilter::class)
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
        $item = Product::query();
        if($params->sortedBy()) {
            $item->orderBy($params->sortedBy(), $params->sortedDir());
        }

        if ($params->hasSearch()) {
            if ($params->hasSearch()) {
                foreach ($params->searchWords() as $word) {
                    $item->where(function ($query) use ($word) {
                        $query->orWhere('product.name', 'like', $word);
                    });
                }
            }
        }

        if ($params->filterFor('id')) {
            $item->where('id', (array)$params->filterFor('id'));
        }

        if ($params->filterFor('vendor')) {
            $item->leftJoin('category', 'category.id', '=', 'product.category_id');
            $item->leftJoin('vendor', 'vendor.id', '=', 'category.vendor_id');

            $item->whereIn('vendor.id', (array)$params->filterFor('vendor'));
        }

        if ($params->filterFor('category')) {
            $item->leftJoin('category', 'category.id', '=', 'product.category_id');

            $item->whereIn('category.id', (array)$params->filterFor('category'));
        }

        return $this->transform($item->paginate(30, ['product.*']));
    }
}
