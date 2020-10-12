<?php

namespace App\Sharp\Entities\Category;

use App\Eloquent\Product\Category;
use App\Sharp\Filters\VendorFilter;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\Eloquent\Transformers\SharpUploadModelAttributeTransformer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\LinkToEntity;

class ListCategory extends SharpEntityList
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
            EntityListDataContainer::make('url')
                ->setLabel('url')
                ->setHtml()
        )->addDataContainer(
            EntityListDataContainer::make('vendor_id')
                ->setLabel('vendor_id')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('parent_id')
                ->setLabel('parent_id')
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
            ->addColumn('url', 4)
            ->addColumn('vendor_id', 2)
            ->addColumn('parent_id', 1)
            ->addColumn('created_at', 2);
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
            ->addFilter('vendors', VendorFilter::class)
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
        $item = Category::query();
        if($params->sortedBy()) {
            $item->orderBy($params->sortedBy(), $params->sortedDir());
        }

        if ($params->hasSearch() || $params->filterFor('vendor')) {
            $item->leftJoin('vendor', 'vendor.id', '=', 'category.vendor_id');

            if ($params->filterFor('vendor')) {
                $item->whereIn('vendor.id', (array)$params->filterFor('vendor'));
            }

            if ($params->hasSearch()) {
                foreach ($params->searchWords() as $word) {
                    $item->where(function ($query) use ($word) {
                        $query->orWhere('category.name', 'like', $word)
                            ->orWhere('vendor.name', 'like', $word);
                    });
                }
            }
        }

//        $this->setCustomTransformer('vendors', function($vendors, $category) {
//            return $category->tags->map(function($tag) {
//                return (new LinkToEntity($tag->label, 'tags'))
//                    ->setTooltip('See related tags')
//                    ->setSearch($tag->title)
//                    ->render();
//            });
//        });

        return $this->transform($item->paginate(10, ['category.*']));
    }
}
