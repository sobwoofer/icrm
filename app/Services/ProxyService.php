<?php

namespace App\Services;

/**
 * Class ProxyService
 * @package App\Services
 * @property string $proxyAuth
 */
class ProxyService
{
    private $proxyAuth;

    public function __construct($config)
    {
        $this->proxyAuth = 'http://' . $config['login'] .':' . $config['password']
            . '@' . $config['host'] . ':'. $config['port'];
    }

    /**
     * @return array
     */
    protected function getParams(): array
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/83.0.4103.61 Chrome/83.0.4103.61 Safari/537.36',
        ];
        $params = [
            'proxy' => $this->proxyAuth,
            'headers' => $headers
        ];

        return $params;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $url, string $method = 'GET', array $params = [])
    {
        $client = new \GuzzleHttp\Client();
        return $client->request($method, $url, array_merge($this->getParams(), $params));
    }

}
