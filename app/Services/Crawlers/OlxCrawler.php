<?php

namespace App\Services\Crawlers;

use App\Eloquent\CustomerCred;
use App\Eloquent\CustomerFilter;
use App\Services\ProxyService;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class OlxCrawler
 * @package App\Services\Crawlers
 * @property ProxyService $proxyService
 */
class OlxCrawler
{
    private $proxyService;

    public function __construct(ProxyService $proxyService)
    {
        $this->proxyService = $proxyService;
    }

    /**
     * @param CustomerFilter $filter
     * @param bool $browser
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function crawl(CustomerFilter $filter, $browser = false): array
    {
        $filterUrl = urldecode($filter->filter_url);

        if (!$browser) {
            $items = $this->crawlByCurl($filterUrl);
        } else {
            $items = $this->crawlByBrowser($filterUrl, $filter->customer->id);
        }

        if ($filter->filter_title) {
            $filteredItems = [];
            foreach ($items as $item) {
                if (stripos($item['title'], $filter->filter_title) !== false) {
                    $filteredItems[] = $item;
                }
            }
            return $filteredItems;
        }

        return $items;
    }

    /**
     * @param string $filterUrl
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function crawlByCurl(string $filterUrl): array
    {
        $response = $this->proxyService->request($filterUrl);
        $body = (string)$response->getBody();

        $crawler = new Crawler();
        $crawler->addHtmlContent($body);
        $first = $crawler->filter('table.offers tr.wrap')->first();

        $items = $crawler->filter('table.offers tr.wrap')->each(function (Crawler $node, $i) {
            $price = '';
            if ($node->filter('.td-price .price')->count()) {
                $price = $node->filter('.td-price .price')->first()->text();
            }
            return [
                'image' => $node->filter('a.thumb img')->attr('src'),
                'url' => $node->filter('.title-cell h3 a')->attr('href'),
                'title' => $node->filter('.title-cell h3 a')->text(),
                'description' => $price
            ];
        });

        return $items;
    }

    public function crawlByBrowser(string $filterUrl, int $customerId): array
    {
        return [];
    }

}
