<?php

namespace App\Sharp\Entities\Category;

use App\Eloquent\Product\Category;
use Code16\Sharp\EntityList\Eloquent\Transformers\SharpUploadModelAttributeTransformer;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;

class ShowCategory extends SharpShow
{
    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    public function find($id): array
    {
        $service = Category::query()->findOrFail($id);

        return $this->transform($service);
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
                ->setLabel('Id:')
        )->addField(
            SharpShowTextField::make('name')
                ->setLabel('name:')
        )->addField(
            SharpShowTextField::make('url')
                ->setLabel('url:')
        )->addField(
            SharpShowTextField::make('vendor_id')
                ->setLabel('vendor_id')
        )->addField(
            SharpShowTextField::make('parent_id')
                ->setLabel('parent_id')
        )->addField(
            SharpShowTextField::make('created_at')
                ->setLabel('Created At:')
        )->addField(
            SharpShowTextField::make('updated_at')
                ->setLabel('Updated At:')
        )->addField(
            SharpShowEntityListField::make('products', 'product')
                ->hideFilterWithValue('category', function($instanceId) {
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
                $column->withSingleField('url');
                $column->withSingleField('vendor_id');
                $column->withSingleField('parent_id');
                $column->withSingleField('created_at');
                $column->withSingleField('updated_at');
            });
        })->addEntityListSection('products', 'products');
    }

    function buildShowConfig()
    {
        //
    }
}
