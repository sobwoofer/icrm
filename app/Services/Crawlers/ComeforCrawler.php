<?php

namespace App\Services\Crawlers;

use App\Events\ProductCrawled;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ComeforCrawler
 * @package App\Services\Crawlers
 */
class ComeforCrawler extends CrawlerAbstract
{

    /**
     * @param string $url
     * @param int $categoryId
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function crawlProductByUrl(string $url, int $categoryId): void
    {
        $crawler = $this->crawlUrl($url);

        $name = trim(str_replace(PHP_EOL, '', $crawler->filter('.product-title h1')->text()));
        $priceText = $crawler->filter('.product-container li.price-text span.price-number')->text();
        $description = $crawler->filter('#tab-description')->html();
        $imageUrl = urldecode($crawler->filter('.main--img')->attr('href'));
        $articleText = $crawler->filter('.vendor')->text();

        $article = trim(str_replace(['Код товара:', PHP_EOL], '', $articleText));

        $price = trim(str_replace(['грн', PHP_EOL], '', $priceText));

        $images = $crawler->filter('#owl-image_products .item')->each(function (Crawler $node, $i) {
            return urldecode($node->filter('a')->attr('href'));
        });

        $priceOptions = $crawler->filter('#product .form-group select option')->each(function (Crawler $node) {
            $name = trim(str_replace(PHP_EOL, '', $node->filter('.name_option')->text()));
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
    protected function getProductLinksByCategoryUrl(string $categoryUrl, bool $firstPage = true)
    {
        $crawler = $this->crawlUrl($categoryUrl);

        $productLinks = $crawler->filter('.product-thumb .caption')->each(function (Crawler $node, $i) {
            return $node->filter('a')->attr('href');
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

    private function prepareUrl(string $rawUrl): string
    {
        $parsedUrl = parse_url($rawUrl);
        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
    }

}
