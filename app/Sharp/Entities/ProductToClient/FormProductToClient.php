<?php

namespace App\Sharp\Entities\ProductToClient;

use App\Eloquent\ClientSite;
use App\Eloquent\ForeignOption;
use App\Eloquent\Product\PriceOption;
use App\Eloquent\Product\Product;
use App\Eloquent\ProductToClient;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Http\WithSharpContext;
use Code16\Sharp\Show\Fields\SharpShowTextField;

class FormProductToClient extends SharpForm
{
    use WithSharpFormEloquentUpdater, WithSharpContext;

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    public function find($id): array
    {
        return $this->transform(
            ProductToClient::query()->with(['clientSite'])->findOrFail($id)
        );
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed the instance id
     */
    public function update($id, array $data)
    {
        $item = $id ? ProductToClient::query()->findOrFail($id) : new ProductToClient;

        /** @var ProductToClient $item */
        $item = $this->save($item, $data);

        if ($this->context()->isCreation()) {
            if ($breadcrumb = $this->context()->getPreviousPageFromBreadcrumb('show')) {
                list($type, $entityKey, $instanceId) = $breadcrumb;
                if ($entityKey == 'product') {
                    $item->product_id = $instanceId;
                }
            }
        }
        $this->save($item, $data);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function delete($id)
    {
        ProductToClient::query()->findOrFail($id)->find($id)->delete();
    }

    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    public function buildFormFields()
    {
        $this->addField(
            SharpFormSelectField::make('client_site_id',
                ClientSite::query()->get()->pluck('slug', 'id')->all()
            )->setLabel('Client Site')
        )->addField(
            SharpShowTextField::make('client_product_id')
                ->setLabel('client_product_id')
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
            $column->withSingleField('client_site_id');
            $column->withSingleField('client_product_id');
            $column->withSingleField('active');
        });
    }
}
