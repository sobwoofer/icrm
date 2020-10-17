<?php

namespace App\Services;

use App\Eloquent\Product\Product;

/**
 * Class ImebliClient
 * @package App\Services
 * @property string $host
 * @property string $token
 */
class ImebliClient
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

    public function createProduct(Product $product)
    {
        $result = false;
        try {
            $response = $this->request(self::CREATE_METHOD, $product);
            if (isset($response->success)) {
                $result = $response->success->product_id;
            }
        } catch (\Exception $e) {

        }

        return $result;
    }

    public function updateProduct(Product $product)
    {
        $result = false;
        try {
            $response = $this->request(self::UPDATE_METHOD, $product);
            if (isset($response->success)) {
                $result = true;
            }
        } catch (\Exception $e) {

        }
        return $result;
    }

    /**
     * @param string $routeMethod
     * @return array
     */
    protected function getParams(string $routeMethod): array
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/83.0.4103.61 Chrome/83.0.4103.61 Safari/537.36',
            'Content-Type' => 'application/json',
        ];
        $jar = new \GuzzleHttp\Cookie\CookieJar;
        return  [
            'headers' => $headers,
            'query' => ['route' => $routeMethod, 'api_key' => $this->token],
            'allow_redirects' => true,
            'cookies' => $jar
        ];
    }


    protected function request(string $routeMethod, $bodyData)
    {
        $params = $this->getParams($routeMethod);
        $url = $this->host . '/index.php';
        $params['body'] = json_encode($bodyData);
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, $params)->getBody();
        return \GuzzleHttp\json_decode((string)$response);
    }
}
