<?php

namespace App\Sharp\Entities\ForeignOption;

use App\Eloquent\ForeignOption;
use App\Eloquent\Product\Product;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowPictureField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Transformers\Attributes\MarkdownAttributeTransformer;

class ShowForeignOption extends SharpShow
{
    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    public function find($id): array
    {
        // Replace/complete this code
        $item = ForeignOption::query()->with('priceOptions')->findOrFail($id);

        return $this->transform($item);
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
            SharpShowTextField::make('foreign_option_id')
                ->setLabel('foreign_option_id:')
        )->addField(
             SharpShowTextField::make('name')
                 ->setLabel('name:')
         )->addField(
             SharpShowTextField::make('created_at')
                 ->setLabel('Created At:')
         )->addField(
             SharpShowTextField::make('updated_at')
                 ->setLabel('Updated At:')
         )->addField(
             SharpShowEntityListField::make('priceOptions', 'priceOption')
                 ->hideFilterWithValue('foreign', function($instanceId) {
                     return $instanceId;
                 })
                 ->showEntityState(false)
                 ->showReorderButton(true)
                 ->showCreateButton()
         );
    }


    /**
     * Build show layout using ->addTab() or ->addColumn()
     *
     * @return void
     */
    public function buildShowLayout()
    {
         $this->addSection('Main', function(ShowLayoutSection $section) {
              $section->addColumn(6, function(ShowLayoutColumn $column) {
                  $column->withSingleField('id');
                  $column->withSingleField('name');
                  $column->withSingleField('foreign_option_id');
                  $column->withSingleField('created_at');
                  $column->withSingleField('updated_at');
              });
         })->addEntityListSection('Price Options', 'priceOptions');
    }

    function buildShowConfig()
    {
        //
    }
}
