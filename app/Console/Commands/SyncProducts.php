<?php

namespace App\Console\Commands;

use App\Eloquent\Product\Product;
use App\Services\ImebliClient;
use Illuminate\Console\Command;
use Log;

/**
 * Class SyncProducts
 * @package App\Console\Commands
 * @property ImebliClient $imebliClient
 */
class SyncProducts extends Command
{
    private const DELAY_BETWEEN_REQUESTS = 2; //sec

    protected $signature = 'sync-products';
    protected $description = 'Command description';

    private $imebliClient;

    public function __construct(
        ImebliClient $imebliClient
    )
    {
        $this->imebliClient = $imebliClient;
        parent::__construct();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(): void
    {
        $this->syncUpdatedProducts();
        $this->syncCreatedProducts();
    }

    private function syncCreatedProducts()
    {
        /** @var  $lastCreatedProducts Product[]*/
        $lastCreatedProducts = Product::query()
            ->where('created_at', '>', $this->getLastDayTime())
            ->where('foreign_product_id','=', null)
            ->where('active',1)
            ->with('priceOptions')->get()->all();

        foreach ($lastCreatedProducts as $product) {
            if ($foreignId = $this->imebliClient->createProduct($product)) {
                $product->updateLastSync($foreignId);
            }
            sleep(self::DELAY_BETWEEN_REQUESTS);
        }
    }

    private function syncUpdatedProducts()
    {
        /** @var  $lastUpdatedProducts Product[]*/
        $lastUpdatedProducts = Product::query()
            ->where('updated_at', '>', $this->getLastDayTime())
            ->where('foreign_product_id','!=', null)
            ->where('active',1)
            ->with('priceOptions')->get()->all();

        foreach ($lastUpdatedProducts as $lastUpdatedProduct) {
            if ($this->imebliClient->updateProduct($lastUpdatedProduct)) {
                $lastUpdatedProduct->updateLastSync();
            }
            sleep(self::DELAY_BETWEEN_REQUESTS);
        }
    }

    private function getLastDayTime()
    {
        return date('Y-m-d h:i:s', strtotime('-1 day'));
    }

    /**
     * @param string $message
     */
    protected function log($message = '')
    {
        $message = $message . PHP_EOL;
        $this->info($message);
        Log::info($message);
    }

}
