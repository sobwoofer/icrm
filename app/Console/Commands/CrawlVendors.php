<?php

namespace App\Console\Commands;

use App\Eloquent\CrawlStat;
use App\Eloquent\Product\Vendor;
use App\Services\Crawlers\ComeforCrawler;
use App\Services\Crawlers\DiplomatCrawler;
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
 * @property DiplomatCrawler $diplomatCrawler
 */
class CrawlVendors extends Command
{

    protected $signature = 'crawl-vendors {vendor_slug?}';
    protected $description = 'Command description';

    private $comeforCrawler;
    private $emmCrawler;
    private $matroluxCrawler;
    private $diplomatCrawler;

    public function __construct(
        ComeforCrawler $comeforCrawler,
        EmmCrawler $emmCrawler,
        MatroluxCrawler $matroluxCrawler,
        DiplomatCrawler $diplomatCrawler
    )
    {
        $this->comeforCrawler = $comeforCrawler;
        $this->emmCrawler = $emmCrawler;
        $this->matroluxCrawler = $matroluxCrawler;
        $this->diplomatCrawler = $diplomatCrawler;
        parent::__construct();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(): void
    {
        $query = Vendor::query();

        if ($vendorSlug = $this->argument('vendor_slug')) {
            $query->where('slug', $vendorSlug);
        }
        $vendors = $query->get();
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
                case Vendor::SLUG_DIPLOMAT:
                    $crawledProducts = $this->diplomatCrawler->crawl($vendor->categories);
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
