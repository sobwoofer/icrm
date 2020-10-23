<?php

namespace App\Sharp\Entities\Product;

use App\Eloquent\ClientSite;
use App\Eloquent\Product\Product;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowPictureField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Transformers\Attributes\MarkdownAttributeTransformer;

class ShowProduct extends SharpShow
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
        $item = Product::query()->with('images')->findOrFail($id);

        $this->setCustomTransformer('description', new MarkdownAttributeTransformer());

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
             SharpShowTextField::make('url')
                 ->setLabel('url:')
         )->addField(
             SharpShowTextField::make('price')
                 ->setLabel('price:')
         )->addField(
             SharpShowTextField::make('article')
                 ->setLabel('article:')
         )->addField(
             SharpShowTextField::make('category_id')
                 ->setLabel('category_id:')
         )->addField(
             SharpShowTextField::make('description')
                 ->setLabel('Description:')
                ->collapseToWordCount(20)
         )->addField(
             SharpShowPictureField::make('image_url')
         )->addField(
             SharpShowTextField::make('created_at')
                 ->setLabel('Created At:')
         )->addField(
             SharpShowTextField::make('updated_at')
                 ->setLabel('Updated At:')
         )->addField(
             SharpShowTextField::make('syncButtons')
         )->addField(
             SharpShowEntityListField::make('productToClient', 'productToClient')
                 ->hideFilterWithValue('product', function($instanceId) {
                     return $instanceId;
                 })
                 ->showEntityState(false)
                 ->showReorderButton(false)
                 ->showCreateButton(true)
         )->addField(
             SharpShowEntityListField::make('image', 'image')
                 ->hideFilterWithValue('product', function($instanceId) {
                     return $instanceId;
                 })
                 ->showEntityState(false)
                 ->showReorderButton(true)
                 ->showCreateButton(false)
         )->addField(
             SharpShowEntityListField::make('priceOptions', 'priceOption')
                 ->hideFilterWithValue('product', function($instanceId) {
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
         $this->addSection('Main', function(ShowLayoutSection $section) {
              $section->addColumn(6, function(ShowLayoutColumn $column) {
                  $column->withSingleField('id');
                  $column->withSingleField('name');
                  $column->withSingleField('image_url');
                  $column->withSingleField('price');
                  $column->withSingleField('url');
                  $column->withSingleField('article');

              });
             $section->addColumn(6, function(ShowLayoutColumn $column) {
                 $column->withSingleField('category_id');
                 $column->withSingleField('description');
                 $column->withSingleField('created_at');
                 $column->withSingleField('updated_at');
             });
         })
             ->addSection('Sync Product Now', function(ShowLayoutSection $section) {
                 $section->addColumn(12, function(ShowLayoutColumn $column) {
                     $column->withSingleField('syncButtons');
                 });
         })
             ->addEntityListSection('Product to Site assignments', 'productToClient')
             ->addEntityListSection('price Options', 'priceOptions')
             ->addEntityListSection('Images', 'image');
    }

    function buildShowConfig()
    {
        $this->setCustomTransformer('syncButtons', function($items, Product $product) {
            $html = 'Product will create if doesn\'t assign to site, or will update if has assignment <br>';
            $clientSites = $product->category->vendor->clientSites;

            foreach ($clientSites as $clientSite) {
                $route = route('run-sync', ['product_id' => $product->id, 'client_site_id' => $clientSite->id]);
                $html .= '<a href="' . $route . '" class="SharpButton SharpButton--accent">Sync with ' . $clientSite->slug  . '</a>';
            }

            return $html;
        });
    }
}
