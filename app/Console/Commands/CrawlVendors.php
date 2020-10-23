<?php

namespace App\Console\Commands;

use App\Eloquent\CrawlStat;
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
        $crawlStat = CrawlStat::create();

        /** @var Vendor $vendor */
        foreach ($vendors as $vendor) {
            $this->log($vendor->name, 'start crawling');

            $crawledProducts = 0;
            switch ($vendor->slug) {
                case Vendor::SLUG_COMEFOR:
                    $crawledProducts = $this->comeforCrawler->crawl($vendor->categories);
                    break;
                case Vendor::SLUG_EMM:
                    $crawledProducts = $this->emmCrawler->crawl($vendor->categories);
                    break;
                case Vendor::SLUG_MATROLUX:
                    $crawledProducts = $this->matroluxCrawler->crawl($vendor->categories);
                    break;
            }

            $crawlStat->incrCrawled($crawledProducts);

            $this->log($vendor->name, 'crawled');
        }
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
