<?php

namespace App\Console\Commands;

use App\Eloquent\Product\Vendor;
use App\Services\Crawlers\ComeforCrawler;
use App\Services\Crawlers\EmmCrawler;
use App\Services\Crawlers\MatroluxCrawler;
use Illuminate\Console\Command;
use Log;

/**
 * Class CrawlVendors
 * @package App\Console\Commands
 * @property ComeforCrawler $comeforCrawler
 * @property EmmCrawler $emmCrawler
 * @property MatroluxCrawler $matroluxCrawler
 */
class CrawlVendors extends Command
{

    protected $signature = 'crawl-vendors';
    protected $description = 'Command description';

    private $comeforCrawler;
    private $emmCrawler;
    private $matroluxCrawler;

    public function __construct(
        ComeforCrawler $comeforCrawler,
        EmmCrawler $emmCrawler,
        MatroluxCrawler $matroluxCrawler
    )
    {
        $this->comeforCrawler = $comeforCrawler;
        $this->emmCrawler = $emmCrawler;
        $this->matroluxCrawler = $matroluxCrawler;
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
                    $this->emmCrawler->crawl($vendor->categories);
                    break;
                case Vendor::SLUG_MATROLUX:
                    $this->matroluxCrawler->crawl($vendor->categories);
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
