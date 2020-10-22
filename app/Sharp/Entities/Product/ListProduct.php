<?php

namespace App\Sharp\Entities\Product;

use App\Eloquent\Product\Product;
use App\Eloquent\ProductToClient;
use App\Sharp\Filters\VendorFilter;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\LinkToEntity;
use Faker\Provider\DateTime;

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
            EntityListDataContainer::make('client_site_id')
                ->setLabel('client_site_id')
//                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('sync_date')
                ->setLabel('sync_date')
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
        ->addColumn('name', 3)
        ->addColumn('client_site_id', 2)
        ->addColumn('sync_date', 2)
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
            ->setDefaultSort('updated_at', 'desc')
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

        if ($params->sortedBy() == 'client_site_id') {
            $item->leftJoin('product_to_client', 'product_to_client.product_id', '=', 'product.id');
        }

        $this->setCustomTransformer('client_site_id', function($items, $product) {
            $html = '';
            /** @var ProductToClient $productToClient */
            foreach ($product->productToClients as $key => $productToClient) {
                $html .= $productToClient->clientSite->slug . ': ' . $productToClient->client_product_id;
            }

            return $html;
        });

        $this->setCustomTransformer('sync_date', function($items, $product) {
            $html = '';
            /** @var ProductToClient $productToClient */
            foreach ($product->productToClients as $key => $productToClient) {
                $date = new \DateTime($productToClient->updated_at);
                $html .= $productToClient->clientSite->slug . ': ' . $date->format('Y-m-d');
            }

            return $html;
        });

        return $this->transform($item->paginate(30, ['product.*']));
    }
}
