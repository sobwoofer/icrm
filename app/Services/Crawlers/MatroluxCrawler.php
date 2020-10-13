<?php

namespace App\Services\Crawlers;

use App\Events\ProductCrawled;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class MatroluxCrawler
 * @package App\Services\Crawlers
 */
class MatroluxCrawler extends CrawlerAbstract
{

    /**
     * @param string $categoryUrl
     * @param bool $firstPage
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getProductLinksByCategoryUrl(string $categoryUrl, bool $firstPage = true)
    {
        $crawler = $this->crawlUrl($categoryUrl);

        $productLinks = $crawler->filter('.product-block > a')->each(function (Crawler $node, $i) {
            return $node->attr('href');
        });

        if ($crawler->filter('.paginationControl a')->count() && $firstPage) {
            $paginationLinks = $crawler->filter('.paginationControl a')->each(function (Crawler $node, $i) use ($categoryUrl) {
                return is_numeric($node->text()) ? $categoryUrl . $node->attr('href') : false;
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
     * @param int $categoryId
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function crawlProductByUrl(string $url, int $categoryId): void
    {
        $crawler = $this->crawlUrl($url);

        $name = trim(str_replace('\n', '', $crawler->filter('.form-title h1')->text()));
        $price = str_replace(['грн', ' ', ' '], '', $crawler->filter('#productCardPrice .price-variant strong')->text());
        $description = $crawler->filter('#tabs-1')->html();
        $imageUrl = urldecode($crawler->filter('.sliderkit-panels meta')->attr('content'));
        $article = $crawler->filter('.form-title meta')->first()->attr('content');

        $images = $crawler->filter('.sliderkit-panels .sliderkit-panel')->each(function (Crawler $node, $i) {
            return urldecode($node->filter('a')->attr('href'));
        });

        //options manipulations
        $priceOptions = $crawler->filter('#configurableParams select#size option, #configurableParams select.select-param option')->each(function (Crawler $node) {
            $name = trim($node->text());
            $price = $node->attr('data-price');
            return ['name' => $name, 'price' => $price];
        });

        $optionPricesNull = 0;
        foreach ($priceOptions as $priceOption) {
            if ($priceOption['price'] === null) {
                $optionPricesNull++;
            }
        }

        $optionsWithoutPrice = $optionPricesNull === count($priceOptions);
        $priceVariantsExists = count($priceOptions) === $crawler->filter('#productCardPrice .price .price-variant')->count();

        if ($optionsWithoutPrice && $priceVariantsExists) {
            $priceOptions = $crawler->filter('#productCardPrice .price .price-variant')->each(function (Crawler $node, $i) use ($priceOptions) {
                $price = trim(str_replace(['грн', ' '],'', $node->text()));
                return ['name' => $priceOptions[$i]['name'], 'price' => $price];
            });
        }
        //options manipulations

        event(new ProductCrawled($name, $description, $url, $imageUrl, $article, $price, $images, $priceOptions, $categoryId));
    }

    private function prepareUrl(string $rawUrl): string
    {
        $parsedUrl = parse_url($rawUrl);
        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
    }

}
