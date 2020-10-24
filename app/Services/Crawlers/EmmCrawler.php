<?php

namespace App\Services\Crawlers;

use App\Events\ProductCrawled;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class EmmCrawler
 * @package App\Services\Crawlers
 */
class EmmCrawler extends CrawlerAbstract
{

    /**
     * @param string $categoryUrl
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getProductLinksByCategoryUrl(string $categoryUrl)
    {
        $crawler = $this->crawlUrl($categoryUrl);

        $productLinks = $crawler->filter('.product-cut__main-info')->each(function (Crawler $node, $i) {
            return $node->filter('.product-photo__item')->attr('data-product-photo-href');
        });

        $nextProductLinks = [];
        if ($crawler->filter('.content__pagination li a')->count()) {
            $previousActive = false;
            $crawler->filter('.content__pagination li')->each(function (Crawler $node, $i) use (&$previousActive, &$nextProductLinks) {
                if ($previousActive) {
                    $previousActive = false;
                    if (is_numeric($node->filter('a')->text())) {
                        $nextProductLinks = $this->getProductLinksByCategoryUrl($node->filter('a')->attr('href'));
                    }
                }

                if (strpos($node->attr('class'), 'paginator__item--active')) {
                    $previousActive = true;
                }
            });
        }

        return array_merge($nextProductLinks, $productLinks);
    }

    public function crawlProductByUrl(string $url, int $categoryId): void
    {
        $crawler = $this->crawlUrl($url);

        $name = trim(str_replace('\n', '', $crawler->filter('h1.content__title')->text()));
        $price = str_replace(' ', '', $crawler->filter('.product-intro .product-price__main .product-price__item-value')->text());

        $description = $crawler->filter('.product-fullinfo .product-fullinfo__inner')->first()->html();
        $imageUrl = urldecode($crawler->filter('#fotoplusprice .product-photo__item--lg')->attr('href'));
        $article = $crawler->filter('#h1headprod span span')->text();

        $images = $crawler->filter('#fotoplusprice .product-photo__thumbs .product-photo__thumb')->each(function (Crawler $node, $i) {
            return urldecode($node->filter('a')->attr('href'));
        });

        $priceOptions = $crawler->filter('.product-intro__variants select option')->each(function (Crawler $node) {
            $name = trim($node->text());
            $price = trim(str_replace(' ', '', $node->attr('data-product-variant--price')));
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
