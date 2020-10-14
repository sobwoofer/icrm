<?php

namespace App\Sharp\Entities\PriceOption;

use App\Eloquent\Product\PriceOption;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;

class ShowPriceOption extends SharpShow
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
        $item = PriceOption::query()->with('foreignOption')->findOrFail($id);

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
            SharpShowTextField::make('name')
                ->setLabel('name:')
        )->addField(
             SharpShowTextField::make('price')
                 ->setLabel('price:')
         )->addField(
             SharpShowTextField::make('foreign_id')
                 ->setLabel('foreign_id:')
         )->addField(
             SharpShowTextField::make('created_at')
                 ->setLabel('Created At:')
         )->addField(
             SharpShowTextField::make('updated_at')
                 ->setLabel('Updated At:')
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
                  $column->withSingleField('price');
                  $column->withSingleField('foreign_id');
                  $column->withSingleField('created_at');
                  $column->withSingleField('updated_at');
              });
         });
    }

    function buildShowConfig()
    {
        //
    }
}
