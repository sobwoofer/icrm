<?php

namespace App\Services\Crawlers;

use App\Events\ProductCrawled;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class DiplomatCrawler
 * @package App\Services\Crawlers
 */
class DiplomatCrawler extends CrawlerAbstract
{

    /**
     * @param string $categoryUrl
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getProductLinksByCategoryUrl(string $categoryUrl)
    {
        $crawler = $this->crawlUrl($categoryUrl);

        $productLinks = $crawler->filter('.product-layout')->each(function (Crawler $node, $i) {
            return $node->filter('.caption .product-name a')->attr('href');
        });

        $nextProductLinks = [];
        if ($crawler->filter('.pagination li')->count()) {
            $previousActive = false;
            $crawler->filter('.pagination li')->each(function (Crawler $node, $i) use (&$previousActive, &$nextProductLinks) {
                if ($previousActive) {
                    $previousActive = false;
                    if (is_numeric($node->filter('a')->text())) {
                        $nextProductLinks = $this->getProductLinksByCategoryUrl($node->filter('a')->attr('href'));
                    }
                }

                if (strpos($node->attr('class'), 'active') !== false) {
                    $previousActive = true;
                }
            });
        }

        return array_merge($nextProductLinks, $productLinks);
    }

    public function crawlProductByUrl(string $url, int $categoryId): void
    {
        $crawler = $this->crawlUrl($url);

        $name = trim($crawler->filter('.container h1')->text());

        if ($crawler->filter('#product .price-new')->count()) {
            $priceText = $crawler->filter('#product .price-new')->text();
        } else {
            $priceText = $crawler->filter('#product .price')->text();
        }
        $price = str_replace([PHP_EOL, '	', 'грн.', ' '], '', $priceText);

        $description = $crawler->filter('#tab-description')->first()->html();
        $imageUrl = urldecode($crawler->filter('.general-image a')->attr('href'));
        $article = $crawler->filter('#prodArticle span')->text();

        $images = $crawler->filter('.image-additional .item')->each(function (Crawler $node, $i) {
            return urldecode($node->filter('a')->attr('href'));
        });

        $priceOptions = $crawler->filter('.options select option')->each(function (Crawler $node) {
            $name = trim($node->text());
            $price = (float)trim(str_replace(' ', '', $node->attr('data-price')));
            return ['name' => $name, 'price' => $price];
        });

        event(new ProductCrawled($name, $description, $url, $imageUrl, $article, $price, $images, $priceOptions, $categoryId));
    }

    private function prepareUrl(string $rawUrl): string
    {
        $parsedUrl = parse_url($rawUrl);
        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
    }

}
