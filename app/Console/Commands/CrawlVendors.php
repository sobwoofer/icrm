<?php

namespace App\Console\Commands;

use App\Eloquent\Product\Image;
use App\Eloquent\Product\PriceOption;
use App\Eloquent\Product\Product;
use App\Eloquent\Product\Vendor;
use App\Services\Crawlers\ComeforCrawler;
use Illuminate\Console\Command;
use Log;

/**
 * Class CrawlVendors
 * @package App\Console\Commands
 * @property ComeforCrawler $comeforCrawler
 */
class CrawlVendors extends Command
{

    protected $signature = 'crawl-vendors';
    protected $description = 'Command description';

    private $comeforCrawler;

    public function __construct(ComeforCrawler $comeforCrawler)
    {
        $this->comeforCrawler = $comeforCrawler;
        parent::__construct();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(): void
    {
        $vendors = Vendor::query()->get();
        $items = [];

        /** @var Vendor $vendor */
        foreach ($vendors as $vendor) {
            $message = 'crawling filter id ' . $vendor->id. PHP_EOL;
            $this->info($message);
            Log::info($message);

            switch ($vendor->slug) {
                case Vendor::SLUG_COMEFOR:
                    $this->comeforCrawler->crawl($vendor->categories);
                    break;
                case Vendor::SLUG_EMM:
                    break;
            }

            $message = 'crawled items ' . count($items). PHP_EOL;
            $this->info($message);
            Log::info($message);
        }
    }



    private function crawlEmm(): array
    {
        return [];
    }

}
