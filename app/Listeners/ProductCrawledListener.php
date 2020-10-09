<?php

namespace App\Listeners;

use App\Eloquent\Product\Image;
use App\Eloquent\Product\PriceOption;
use App\Eloquent\Product\Product;
use App\Events\ProductCrawled;

/**
 * Class ProductCrawledListener
 * @package App\Listeners
 */
class ProductCrawledListener
{

    /**
     * @param ProductCrawled $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(ProductCrawled $event)
    {
        $product = new Product();
        $product->name = $event->name;
        $product->description = $event->description;
        $product->url = $event->url;
        $product->image_url = $event->imageUrl;
        $product->article = $event->article;
        $product->price = $event->price;
        $product->category_id = $event->categoryId;
        $product->save();

        foreach ($event->images as $url) {
            $image = new Image();
            $image->url = $url;
            $image->product_id = $product->id;
            $image->save();
        }

        foreach ($event->priceOptions as $priceOptionArray) {
            $priceOption = new PriceOption();
            $priceOption->name = $priceOptionArray['name'];
            $priceOption->price = $priceOptionArray['price'];
            $priceOption->product_id = $product->id;
            $priceOption->save();
        }

    }
}
