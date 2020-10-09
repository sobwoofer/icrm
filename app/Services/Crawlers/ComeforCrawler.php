<?php

namespace App\Services\Crawlers;

use App\Eloquent\Product\Category;
use App\Events\ProductCrawled;
use App\Services\ProxyService;
use Symfony\Component\DomCrawler\Crawler;
use Exception;
use Log;

/**
 * Class ComeforCrawler
 * @package App\Services\Crawlers
 */
class ComeforCrawler
{
    private $proxyService;

    public function __construct(ProxyService $proxyService)
    {
        $this->proxyService = $proxyService;
    }

    /**
     * @param $categories
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function crawl($categories): void
    {
        /** @var Category $category */
        foreach ($categories as $category) {
            $productLinks = $this->getProductLinksByCategoryUrl($category->url);

            foreach ($productLinks as $productLink) {
                try {
                    $this->crawlProductByUrl($productLink, $category->id);
                } catch (Exception $e) {
                    Log::error('cant_parse_product: ' . $productLink . ' | ' . $e->getMessage());
                }
                sleep(1);
            }
        }
    }

    public function crawlProductByUrl(string $url, int $categoryId): void
    {
        $crawler = $this->crawlUrl($url);

        $name = $crawler->filter('.product-title h1')->text();
        $priceText = $crawler->filter('.product-container li.price-text span.price-number')->text();
        $description = $crawler->filter('#tab-description')->html();
        $imageUrl = urldecode($crawler->filter('.main--img')->attr('href'));
        $articleText = $crawler->filter('.vendor')->text();

        $article = str_replace('Код товара: ', '', $articleText);
        $price = str_replace('грн', '', $priceText);

        $images = $crawler->filter('#owl-image_products .item')->each(function (Crawler $node, $i) {
            return urldecode($node->filter('a')->attr('href'));
        });

        $priceOptions = $crawler->filter('#product .form-group select option')->each(function (Crawler $node) {
            $name = $node->filter('.name_option')->text();
            foreach ($node->filter('span') as $children) {
                $children->parentNode->removeChild($children);
            }
            $price = trim(str_replace('=', '', $node->text()));
            return ['name' => $name, 'price' => $price];
        });

        event(new ProductCrawled($name, $description, $url, $imageUrl, $article, $price, $images, $priceOptions, $categoryId));
    }


    /**
     * @param string $categoryUrl
     * @param bool $firstPage
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getProductLinksByCategoryUrl(string $categoryUrl, bool $firstPage = true)
    {
        $crawler = $this->crawlUrl($categoryUrl);

        $productLinks = $crawler->filter('.product-thumb')->each(function (Crawler $node, $i) {
            return $node->filter('h4 a')->attr('href');
        });

        if ($crawler->filter('.pagination li a')->count() && $firstPage) {
            $paginationLinks = $crawler->filter('.pagination li a')->each(function (Crawler $node, $i) {
                return is_numeric($node->text()) ? $node->attr('href') : false;
            });
            foreach ($paginationLinks as $paginationLink) {
                if ($paginationLink) {
                    $nextProductLinks = $this->getProductLinksByCategoryUrl($paginationLink, false);
                    $productLinks = array_merge($nextProductLinks, $productLinks);
                }
            }
        }

        return $productLinks;
    }

    /**
     * @param string $url
     * @return Crawler
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function crawlUrl(string $url): Crawler
    {
        $response = $this->proxyService->request($url);
        $body = (string)$response->getBody();

        $crawler = new Crawler();
        $crawler->addHtmlContent($body);
        return $crawler;
    }

    private function prepareUrl(string $rawUrl): string
    {
        $parsedUrl = parse_url($rawUrl);
        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
    }

}
