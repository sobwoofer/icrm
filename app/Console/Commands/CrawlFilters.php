<?php

namespace App\Console\Commands;

use App\Eloquent\CustomerFilter;
use App\Eloquent\CustomerItem;
use App\Services\Crawlers\IaaiCrawler;
use App\Services\Crawlers\OlxCrawler;
use Illuminate\Console\Command;
use Log;

/**
 * Class CrawlFilters
 * @package App\Console\Commands
 * @property IaaiCrawler $iaaiCrawler
 * @property OlxCrawler $olxCrawler
 */
class CrawlFilters extends Command
{

    protected $signature = 'crawl-filters';
    protected $description = 'Command description';

    private $iaaiCrawler;
    private $olxCrawler;

    public function __construct(IaaiCrawler $iaaiCrawler, OlxCrawler $olxCrawler)
    {
        $this->iaaiCrawler = $iaaiCrawler;
        $this->olxCrawler = $olxCrawler;
        parent::__construct();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(): void
    {
        $filters = CustomerFilter::query()->where('enabled', true)->get();
        $items = [];

        /** @var CustomerFilter $filter */
        foreach ($filters as $filter) {
            $message = 'crawling filter id ' . $filter->id. PHP_EOL;
            $this->info($message);
            Log::info($message);
            switch ($filter->spot_type) {
                case CustomerFilter::SPOT_IAAI:
                    $items = $this->iaaiCrawler->crawl($filter);
                    break;
                case CustomerFilter::SPOT_OLX:
                    $items = $this->olxCrawler->crawl($filter);
                    break;
                case CustomerFilter::SPOT_AUTORIA:
                    $items = $this->checkFilterAutoria($filter);
                    break;
            }
            $itemsExistBefore = $filter->items;

            $message = 'crawled items ' . count($items). PHP_EOL;
            $this->info($message);
            Log::info($message);

            foreach ($items as $item) {
                $customerItem = new CustomerItem();
                $customerItem->image = $item['image'];
                $customerItem->url = $item['url'];
                $customerItem->title = $item['title'];
                $customerItem->description = $item['description'];
                $customerItem->filter_id = $filter->id;
                $customerItem->save();
            }

            if (!count($itemsExistBefore)) {
                event(new FirstFilterCrawled(count($items), $filter));
            }

        }
    }


    private function checkFilterOlx(CustomerFilter $filter): array
    {
        return [];
    }

    private function checkFilterAutoria(CustomerFilter $filter): array
    {
        return [];
    }

}
