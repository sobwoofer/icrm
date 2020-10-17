<?php

namespace App\Console\Commands;

use App\Eloquent\Product\Product;
use App\Services\ImebliClient;
use Illuminate\Console\Command;
use Log;

/**
 * Class SendUpdatedProducts
 * @package App\Console\Commands
 * @property ImebliClient $imebliClient
 */
class SendUpdatedProducts extends Command
{
    private const DELAY_BETWEEN_REQUESTS = 2; //sec

    protected $signature = 'send-products';
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
        $lastUpdatedProducts = Product::query()
           ->where('updated_at', '>', $this->getLastDayTime())
           ->where('active',1)->get()->all();

        foreach ($lastUpdatedProducts as $lastUpdatedProduct) {
            $this->imebliClient->updateProduct();
        }

    }

    private function getLastDayTime(): string
    {
        return date('Y-m-d H:i:s', strtotime('-1 day'));
    }

    /**
     * @param string $vendorName
     * @param string $message
     */
    protected function log(string $vendorName, $message = '')
    {
        $message = $message . '_' . $vendorName . PHP_EOL;
        $this->info($message);
        Log::info($message);
    }

}
