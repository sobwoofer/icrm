<?php

namespace App\Sharp\Entities\ClientSite;

use App\Eloquent\ClientSite;
use App\Eloquent\Product\Vendor;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\Fields\SharpShowTextField;

class FormClientSite extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    public function find($id): array
    {
        return $this->transform(
            ClientSite::query()->with('vendors')->findOrFail($id)
        );
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed the instance id
     */
    public function update($id, array $data)
    {
        $item = $id ? ClientSite::query()->findOrFail($id) : new ClientSite;
        $this->save($item, $data);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function delete($id)
    {
        ClientSite::query()->findOrFail($id)->find($id)->delete();
    }

    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    public function buildFormFields()
    {
        $this->addField(
            SharpShowTextField::make('slug')
                ->setLabel('slug')
        )->addField(
            SharpShowTextField::make('url')
                ->setLabel('url')
        )->addField(
            SharpFormSelectField::make('type', [
                ClientSite::TYPE_OPENCART => ClientSite::TYPE_OPENCART,
                ClientSite::TYPE_OTHER => ClientSite::TYPE_OTHER
            ])->setLabel('type')
        )->addField(
            SharpShowTextField::make('url')
                ->setLabel('url')
        )->addField(
            SharpShowTextField::make('auth_key')
                ->setLabel('auth_key')
        )->addField(
            SharpFormSelectField::make('active', [1 => 'on', 0 => 'off'])
                ->setLabel('active')
        )->addField(
            SharpFormTagsField::make('vendors',
                Vendor::orderBy('name')->get()->pluck('name', 'id')->all()
            )->setLabel('Vendors')
                ->setCreatable(true)
                ->setCreateAttribute('name')
                ->setMaxTagCount(4)

        );
    }

    /**
     * Build form layout using ->addTab() or ->addColumn()
     *
     * @return void
     */
    public function buildFormLayout()
    {
        $this->addColumn(6, function(FormLayoutColumn $column) {
            $column->withSingleField('slug');
            $column->withSingleField('url');
            $column->withSingleField('type');
            $column->withSingleField('auth_key');
            $column->withSingleField('vendors');
            $column->withSingleField('active');
        });
    }
}
