<?php

namespace App\Sharp\Entities\ProductToClient;

use App\Eloquent\Product\Category;
use App\Eloquent\Product\Image;
use App\Eloquent\Product\PriceOption;
use App\Eloquent\ProductToClient;
use App\Sharp\Filters\ProductFilter;
use App\Sharp\Filters\VendorFilter;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\Eloquent\Transformers\SharpUploadModelAttributeTransformer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\LinkToEntity;

class ListProductToClient extends SharpEntityList
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
            EntityListDataContainer::make('client_product_id')
                ->setLabel('client_product_id')
        )->addDataContainer(
            EntityListDataContainer::make('active')
                ->setLabel('active')
        )->addDataContainer(
            EntityListDataContainer::make('product')
                ->setLabel('product')
        )->addDataContainer(
            EntityListDataContainer::make('client_site')
                ->setLabel('client_site')
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
            ->addColumn('client_site',  2)
            ->addColumn('product', 2)
            ->addColumn('client_product_id', 2)
            ->addColumn('active', 1)
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
            ->setSearchable(false)
            ->setDefaultSort('id', 'asc')
            ->addFilter('product', ProductFilter::class)
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
        $item = ProductToClient::query()->with(['product', 'clientSite']);
        if($params->sortedBy()) {
            $item->orderBy($params->sortedBy(), $params->sortedDir());
        }

        if ($params->hasSearch() || $params->filterFor('product')) {
            $item->leftJoin('product', 'product.id', '=', 'product_to_client.product_id');

            if ($params->filterFor('product')) {
                $item->whereIn('product.id', (array)$params->filterFor('product'));
            }
        }

        $this->setCustomTransformer('product', function($vendors, ProductToClient $item) {
            return $item->product->name;
        });

        $this->setCustomTransformer('client_site', function($vendors, ProductToClient $item) {
            return $item->clientSite->slug;
        });

        return $this->transform($item->paginate(30, ['product_to_client.*']));
    }
}
