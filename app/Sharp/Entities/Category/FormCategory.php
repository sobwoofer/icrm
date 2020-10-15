<?php

namespace App\Sharp\Entities\Category;

use App\Eloquent\ForeignOption;
use App\Eloquent\Product\Category;
use App\Eloquent\Product\PriceOption;
use App\Eloquent\Product\Vendor;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\Fields\SharpShowTextField;

class FormCategory extends SharpForm
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
            Category::query()->with('vendor')->findOrFail($id)
        );
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed the instance id
     */
    public function update($id, array $data)
    {
        $item = $id ? Category::query()->findOrFail($id) : new Category;
        $this->save($item, $data);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function delete($id)
    {
        Category::query()->findOrFail($id)->find($id)->delete();
    }

    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    public function buildFormFields()
    {
        $this->addField(
            SharpShowTextField::make('name')
                ->setLabel('name')
        )->addField(
            SharpFormTextField::make('url')
                ->setLabel('url')
        )->addField(
            SharpFormSelectField::make('vendor_id',
                Vendor::orderBy('id')->get()->pluck('name', 'id')->all()
            )
                ->setDisplayAsDropdown()
                ->setLabel('Vendor')
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
            $column->withSingleField('name');
            $column->withSingleField('url');
            $column->withSingleField('vendor_id');
        });
    }
}
