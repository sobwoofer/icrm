<?php

namespace App\Sharp\Entities\Vendor;

use App\Eloquent\Product\Vendor;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;

class ShowVendor extends SharpShow
{
    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    public function find($id): array
    {
        $entity = Vendor::query()->findOrFail($id);
        // Replace/complete this code

        return $this->transform($entity);
    }

    /**
     * Build show fields using ->addField()
     *
     * @return void
     */
    public function buildShowFields()
    {
        $this->addField(
            SharpShowTextField::make('id')
        )->addField(
            SharpShowTextField::make('name')
        )->addField(
            SharpShowTextField::make('slug')
        )->addField(
            SharpShowTextField::make('site_url')
        )->addField(
            SharpShowTextField::make('created_at')
        )->addField(
            SharpShowTextField::make('updated_at')
        )->addField(
            SharpShowEntityListField::make('categories', 'category')
                ->hideFilterWithValue('vendor', function($instanceId) {
                    return $instanceId;
                })
                ->showEntityState(false)
                ->showReorderButton(true)
                ->showCreateButton(false)
        );
    }

    /**
     * Build show layout using ->addTab() or ->addColumn()
     *
     * @return void
     */
    public function buildShowLayout()
    {
         $this->addSection('Section', function(ShowLayoutSection $section) {
              $section->addColumn(6, function(ShowLayoutColumn $column) {
                  $column->withSingleField('id');
                  $column->withSingleField('name');
                  $column->withSingleField('slug');
                  $column->withSingleField('site_url');
                  $column->withSingleField('created_at');
                  $column->withSingleField('updated_at');
              });
         })->addEntityListSection('categories', 'categories');
    }

    function buildShowConfig()
    {
        //
    }
}
