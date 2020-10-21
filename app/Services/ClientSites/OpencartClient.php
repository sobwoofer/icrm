<?php

namespace App\Services\ClientSites;

use App\Eloquent\Product\Product;

/**
 * Class OpencartClient
 * @package App\Services
 * @property string $host
 * @property string $token
 */
class OpencartClient implements ClientSiteInterface
{
    public const CREATE_METHOD = 'api/product/add';
    public const UPDATE_METHOD = 'api/product/update';

    private $host;
    private $token;

    public function __construct(string $host, string $token)
    {
        $this->host = $host;
        $this->token = $token;
    }

    public function createProduct(Product $product): ?string
    {
        $result = null;
        try {
            $response = $this->request(self::CREATE_METHOD, $this->prepareProductToSend($product));
            if (isset($response->success)) {
                $result = $response->success->product_id;
            }
        } catch (\Exception $e) {

        }

        return $result;
    }

    public function updateProduct(Product $product): ?string
    {
        $result = null;
        try {
            $response = $this->request(self::UPDATE_METHOD, $this->prepareProductToSend($product));
            if (isset($response->success)) {
                $result = true;
            }
        } catch (\Exception $e) {

        }
        return $result;
    }

    private function prepareProductToSend(Product $productOrigin)
    {
        $product = new \stdClass();
        $product->article = $productOrigin->article;
        $product->name = $productOrigin->name;
        $product->mainImage = $productOrigin->image_url;
        $product->description = $productOrigin->description;
        $product->price = $productOrigin->price;

        $product->images = [];
        foreach ($productOrigin->images as $image) {
            $product->images[] = $image->url;
        }

        $product->options = [];
        foreach ($productOrigin->syncPriceOptions as $optionOrigin) {
            $option = new \stdClass();
            $option->name = $optionOrigin->foreignOption->name;
            $option->price = $optionOrigin->price;
            $product->options[] = $option;
        }

        return $product;
    }

    /**
     * @param string $routeMethod
     * @param string $body
     * @return array
     */
    protected function getParams(string $routeMethod, string $body): array
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/83.0.4103.61 Chrome/83.0.4103.61 Safari/537.36',
            'Content-Type' => 'application/json',
        ];
        $jar = new \GuzzleHttp\Cookie\CookieJar;
        return  [
            'headers' => $headers,
            'query' => ['route' => $routeMethod, 'api_key' => $this->token, 'store_flag' => 'imebli'],
            'allow_redirects' => true,
            'cookies' => $jar,
            'body' => $body
        ];
    }

    protected function request(string $routeMethod, $bodyData)
    {
        $params = $this->getParams($routeMethod, json_encode($bodyData));
        $url = $this->host . '/index.php';
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, $params)->getBody();
        return \GuzzleHttp\json_decode((string)$response);
    }
}
