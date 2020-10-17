<?php

namespace App\Services;

/**
 * Class ImebliClient
 * @package App\Services
 * @property string $host
 * @property string $token
 */
class ImebliClient
{
    public const CREATE_URL_METHOD = '/index.php?route=api/product/add';
    public const UPDATE_URL_METHOD = '/index.php?route=api/product/update';

    private $host;
    private $token;

    public function __construct(string $host, string $token)
    {
        $this->host = $host;
        $this->token = $token;
    }

    public function createProduct()
    {

    }

    public function updateProduct()
    {

    }

    /**
     * @return array
     */
    protected function getParams(): array
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/83.0.4103.61 Chrome/83.0.4103.61 Safari/537.36',
        ];
        return  [
            'headers' => $headers
        ];
    }

    protected function request(string $url, $message = [])
    {
        $client = new \GuzzleHttp\Client();
        return $client->request('POST', $url, $this->getParams());
    }
}
