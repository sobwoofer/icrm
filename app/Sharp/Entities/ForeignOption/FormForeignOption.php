<?php

namespace App\Sharp\Entities\ForeignOption;

use App\Eloquent\ForeignOption;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;

class FormForeignOption extends SharpForm
{
    use WithSharpFormEloquentUpdater;


    public function find($id): array
    {
        return $this->transform(
            ForeignOption::query()->findOrFail($id)
        );
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed the instance id
     */
    public function update($id, array $data)
    {
        $item = $id ? ForeignOption::findOrFail($id) : new ForeignOption;
        $this->save($item, $data);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function delete($id)
    {
        ForeignOption::query()->findOrFail($id)->find($id)->delete();
    }

    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    public function buildFormFields()
    {
        $this->addField(
            SharpFormTextField::make('foreign_option_id')
                ->setLabel('foreign_option_id')
        )->addField(
            SharpFormTextField::make('name')
                ->setLabel('name')
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
            $column->withSingleField('name');
        });
    }
}
