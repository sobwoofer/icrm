<?php

namespace App\Sharp\Entities\PriceOption;

use App\Eloquent\Product\Category;
use App\Eloquent\Product\PriceOption;
use App\Sharp\Filters\ProductFilter;
use App\Sharp\Filters\VendorFilter;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\Eloquent\Transformers\SharpUploadModelAttributeTransformer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\LinkToEntity;

class ListPriceOption extends SharpEntityList
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
        )->addDataContainer(
            EntityListDataContainer::make('price')
                ->setLabel('price')
                ->setHtml()
        )->addDataContainer(
            EntityListDataContainer::make('product_id')
                ->setLabel('product_id')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('foreignOption')
                ->setLabel('foreign_Option')
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
            ->addColumn('name',  2)
            ->addColumn('price', 1)
            ->addColumn('product_id', 2)
            ->addColumn('foreignOption', 2)
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
            ->setDefaultSort('name', 'asc')
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
        $item = PriceOption::query();
        if($params->sortedBy()) {
            $item->orderBy($params->sortedBy(), $params->sortedDir());
        }

        if ($params->filterFor('foreign')) {
            $item->leftJoin('foreign_option', 'foreign_option.id', '=', 'price_option.foreign_option_id');
            $item->whereIn('foreign_option.id', (array)$params->filterFor('foreign'));
        }

        if ($params->hasSearch() || $params->filterFor('product')) {
            $item->leftJoin('product', 'product.id', '=', 'price_option.product_id');

            if ($params->filterFor('product')) {
                $item->whereIn('product.id', (array)$params->filterFor('product'));
            }

            if ($params->hasSearch()) {
                foreach ($params->searchWords() as $word) {
                    $item->where(function ($query) use ($word) {
                        $query->orWhere('product.name', 'like', $word);
                    });
                }
            }
        }

        $this->setCustomTransformer('foreignOption', function($value, PriceOption $item) {
                return  $item->foreignOption ? $item->foreignOption->name : '';
            });

        return $this->transform($item->paginate(30, ['price_option.*']));
    }
}
