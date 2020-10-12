<?php

namespace App\Listeners;

use App\Eloquent\CrawlStat;
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
        /** @var Product $product */
        if ($product = $this->getProduct($event->article)){
            $this->fillAndSaveProduct($product, $event);

            if ($event->priceOptions) {
                $this->updatePriceOptions($product, $event->priceOptions);
            }
        } else {
            $product = $this->fillAndSaveProduct(new Product(), $event);

            if ($event->priceOptions) {
                $this->createPriceOptions($product, $event->priceOptions);
            }
        }
    }

    /**
     * @param Product $product
     * @param array $eventPriceOptions
     */
    private function updatePriceOptions(Product $product, array $eventPriceOptions)
    {
        foreach ($eventPriceOptions as $priceOptionArray) {
            $foundOption = false;
            foreach ($product->priceOptions as $priceOption) {
                if ($priceOptionArray['name'] === $priceOption->name) {
                    $priceOption->price = $priceOptionArray['price'];
                    $priceOption->save();
                    $foundOption = true;
                    break;
                }
            }
            if (!$foundOption) {
                $this->createPriceOptions($product, [$priceOptionArray]);
            }
        }
    }

    /**
     * @param Product $product
     * @param array $productOptions
     */
    private function createPriceOptions(Product $product, array $productOptions)
    {
        foreach ($productOptions as $priceOptionArray) {
            $priceOption = new PriceOption();
            $priceOption->name = $priceOptionArray['name'];
            $priceOption->price = $priceOptionArray['price'];
            $priceOption->product_id = $product->id;
            $priceOption->save();
        }
    }

    /**
     * @return CrawlStat
     */
    private function getLastCrawlStat(): CrawlStat
    {
        /** @var CrawlStat $crawlStat */
        $crawlStat =  CrawlStat::query()->orderBy('created_at', 'desc')->first();
        return $crawlStat;
    }

    /**
     * save() will check if something in the model has changed.
     * If it hasn't it won't run a db query.
     * @param Product $product
     * @param ProductCrawled $event
     * @return Product
     */
    private function fillAndSaveProduct(Product $product, ProductCrawled $event): Product
    {
        $product->name = $event->name;
        $product->description = $event->description;
        $product->url = $event->url;
        $product->image_url = $event->imageUrl;
        $product->article = $event->article;
        $product->price = $event->price;
        $product->category_id = $event->categoryId;

        if ($product->getDirty()) {
            if ($product->id) {
                $this->getLastCrawlStat()->incrUpdated();
            } else {
                $this->getLastCrawlStat()->incrCreated();
            }
        }
        $product->save();

        return $product;
    }

    private function getProduct(string $article)
    {
        return Product::query()->where('article', $article)->first();
    }
}
