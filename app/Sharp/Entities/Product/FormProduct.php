<?php

namespace App\Sharp\Entities\Product;

use App\Eloquent\ForeignOption;
use App\Eloquent\Product\Category;
use App\Eloquent\Product\PriceOption;
use App\Eloquent\Product\Product;
use App\Eloquent\Product\Vendor;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\Fields\SharpShowTextField;

class FormProduct extends SharpForm
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
            Product::query()->with('category')->findOrFail($id)
        );
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed the instance id
     */
    public function update($id, array $data)
    {
        $item = $id ? Product::query()->findOrFail($id) : new Product;
        $this->save($item, $data);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function delete($id)
    {
        Product::query()->findOrFail($id)->find($id)->delete();
    }

    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    public function buildFormFields()
    {
        $this->addField(
            SharpShowTextField::make('foreign_id')
                ->setLabel('foreign_id')
        )->addField(
            SharpFormSelectField::make('active', [1 => 'on', 0 => 'off'])
                ->setLabel('active')
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
            $column->withSingleField('foreign_id');
            $column->withSingleField('active');
        });
    }
}
