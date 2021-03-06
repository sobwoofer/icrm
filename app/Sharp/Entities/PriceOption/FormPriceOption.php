<?php

namespace App\Sharp\Entities\PriceOption;

use App\Eloquent\ForeignOption;
use App\Eloquent\Product\PriceOption;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;

class FormPriceOption extends SharpForm
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
            PriceOption::query()->with('foreignOption')->findOrFail($id)
        );
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed the instance id
     */
    public function update($id, array $data)
    {
        $item = $id ? PriceOption::query()->findOrFail($id) : new PriceOption;
        $this->save($item, $data);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function delete($id)
    {
        PriceOption::query()->findOrFail($id)->find($id)->delete();
    }

    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    public function buildFormFields()
    {
        $this->addField(
            SharpFormSelectField::make('foreign_option_id',
                ForeignOption::orderBy('id')->get()->pluck('name', 'id')->all()
            )->setLabel('Foreign Option')
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
            $column->withSingleField('foreign_option_id');
        });
    }
}
