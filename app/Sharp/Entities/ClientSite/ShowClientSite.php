<?php

namespace App\Sharp\Entities\ClientSite;

use App\Eloquent\ClientSite;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;

class ShowClientSite extends SharpShow
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
        $item = ClientSite::query()->findOrFail($id);

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
            SharpShowTextField::make('slug')
                ->setLabel('slug:')
        )->addField(
             SharpShowTextField::make('url')
                 ->setLabel('url:')
         )->addField(
             SharpShowTextField::make('type')
                 ->setLabel('type:')
         )->addField(
             SharpShowTextField::make('auth_key')
                 ->setLabel('auth_key:')
         )->addField(
             SharpShowTextField::make('active')
                 ->setLabel('active:')
         )->addField(
             SharpShowTextField::make('created_at')
                 ->setLabel('Created At:')
         )->addField(
             SharpShowTextField::make('updated_at')
                 ->setLabel('Updated At:')
         )->addField(
             SharpShowEntityListField::make('vendors', 'vendor')
                 ->hideFilterWithValue('client_site', function($instanceId) {
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
                  $column->withSingleField('slug');
                  $column->withSingleField('url');
                  $column->withSingleField('type');
                  $column->withSingleField('auth_key');
                  $column->withSingleField('active');
                  $column->withSingleField('created_at');
                  $column->withSingleField('updated_at');
              });
         })->addEntityListSection('Vendors for sync', 'vendors');;
    }

    function buildShowConfig()
    {
        //
    }
}
