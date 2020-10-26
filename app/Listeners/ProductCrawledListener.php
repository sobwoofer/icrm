<?php

namespace App\Listeners;

use App\Eloquent\CrawlStat;
use App\Eloquent\ForeignOption;
use App\Eloquent\Product\Image;
use App\Eloquent\Product\PriceOption;
use App\Eloquent\Product\Product;
use App\Events\CreatedProduct;
use App\Events\ProductCrawled;
use Log;

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
        if ($product = $this->getProduct($event->article)) {
            $this->fillAndSaveProduct($product, $event);

            if ($event->priceOptions) {
                $this->updatePriceOptions($product, $event->priceOptions);
            }
        } else {
            $product = $this->fillAndSaveProduct(new Product(), $event);

            if ($event->priceOptions) {
                $this->createPriceOptions($product, $event->priceOptions);
            }

            if ($event->images) {
                $this->createImages($product, $event->images);
            }
            event(new CreatedProduct($product->name, $product->url, $product->price));
        }
    }

    /**
     * @param Product $product
     * @param array $images
     */
    private function createImages(Product $product, array $images)
    {
        foreach ($images as $image) {
            $imageModel = new Image();
            $imageModel->url = $image;
            $imageModel->product_id = $product->id;
            $imageModel->save();
        }
    }

    /**
     * @param Product $product
     * @param array $eventPriceOptions
     */
    private function updatePriceOptions(Product $product, array $eventPriceOptions)
    {
        $updatedSomeOptions = false;
        foreach ($eventPriceOptions as $priceOptionArray) {
            $foundOption = false;
            foreach ($product->priceOptions as $priceOption) {
                if ($priceOptionArray['name'] === $priceOption->name) {
                    $priceOption->price = $priceOptionArray['price'];
                    if ($priceOption->isDirty()) {
                        $priceOption->save();
                        $updatedSomeOptions = true;
                    }
                    $foundOption = true;
                    break;
                }
            }
            if (!$foundOption) {
                $this->createPriceOptions($product, [$priceOptionArray]);
            }
        }
        if ($updatedSomeOptions) {
            Log::info('Updated price_options for product id ' . $product->id);
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
            $priceOption->foreign_option_id = $this->resolveForeignOptionIdByName($priceOptionArray['name']);
            $priceOption->save();
        }
    }

    private function resolveForeignOptionIdByName(string $priceOptionName): ?int
    {
        preg_match_all('!\d+!', $priceOptionName, $matches);

        if (!empty($matches[0]) && count($matches[0]) === 2) {
            $firstSize = (int)$matches[0][0];
            $secondSize = (int)$matches[0][1];

            $sizeCombinations = [
                $firstSize . 'x' . $secondSize,
                $secondSize . 'x' . $firstSize,
                $firstSize * 10 . 'x' . $secondSize * 10,
                $secondSize * 10 . 'x' . $firstSize * 10,
            ];

            return ForeignOption::query()->whereIn('name', $sizeCombinations)->pluck('id')->first();
        }

        return null;
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

        if ($product->id && $product->isDirty('price')) {
            $product->save();
            Log::info('Updated price for product id ' . $product->id);
            $this->getLastCrawlStat()->incrUpdated();
        }

        if (!$product->id && $product->getDirty()) {
            $product->save();
            Log::info('Created new product id ' . $product->id);
            $this->getLastCrawlStat()->incrCreated();
        }

        return $product;
    }

    private function getProduct(string $article)
    {
        return Product::query()->where('article', $article)->first();
    }
}
