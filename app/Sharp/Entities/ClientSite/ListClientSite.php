<?php

namespace App\Sharp\Entities\ClientSite;

use App\Eloquent\ClientSite;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

class ListClientSite extends SharpEntityList
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
            EntityListDataContainer::make('url')
                ->setLabel('url')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('active')
                ->setLabel('active')
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
        ->addColumn('url', 3)
        ->addColumn('active', 2)
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
        $item = ClientSite::query();

        if($params->sortedBy()) {
            $item->orderBy($params->sortedBy(), $params->sortedDir());
        }

        if ($params->hasSearch()) {
            if ($params->hasSearch()) {
                foreach ($params->searchWords() as $word) {
                    $item->where(function ($query) use ($word) {
                        $query->orWhere('client_site.slug', 'like', $word);
                    });
                }
            }
        }

        if ($params->filterFor('id')) {
            $item->where('id', (array)$params->filterFor('id'));
        }

        return $this->transform($item->paginate(30, ['client_site.*']));
    }
}
