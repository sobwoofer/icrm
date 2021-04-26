<?php

namespace App\Services\Crawlers;

use App\Eloquent\CrawlStat;
use App\Eloquent\Product\Category;
use App\Services\ProxyService;
use Symfony\Component\DomCrawler\Crawler;
use Exception;
use Log;

/**
 * Class CrawlerAbstract
 * @package App\Services\Crawlers
 * @property ProxyService $proxyService
 */
class CrawlerAbstract
{
    private const SLEEP_BETWEEN_REQUESTS = 2;

    private $proxyService;

    public function __construct(ProxyService $proxyService)
    {
        $this->proxyService = $proxyService;
    }

    /**
     * @param $categories
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function crawl($categories): int
    {
        $crawled = 0;
        /** @var Category $category */
        foreach ($categories as $category) {
            $productLinks = $this->getProductLinksByCategoryUrl($category->url);

            foreach ($productLinks as $productLink) {
                try {
                    $this->crawlProductByUrl($productLink, $category->id);
                    $crawled++;
                } catch (Exception $e) {
                    Log::error('cant_parse_product: ' . $productLink . ' | ' . $e->getMessage());
                }
                sleep(self::SLEEP_BETWEEN_REQUESTS);
            }
        }

        return $crawled;
    }

    /**
     * @param string $url
     * @return Crawler
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function crawlUrl(string $url): Crawler
    {
        $response = $this->proxyService->request($url);
        $body = (string)$response->getBody();

        $crawler = new Crawler();
        $crawler->addHtmlContent($body);
        return $crawler;
    }
}
