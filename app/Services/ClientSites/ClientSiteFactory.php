<?php

namespace App\Services\ClientSites;

use App\Eloquent\ClientSite;

/**
 * Class ClientSiteFactory
 * @package App\Services\ClientSites
 * @property ClientSiteInterface $client
 */
class ClientSiteFactory
{
    private $client;

    public function __construct(ClientSite $clientSite)
    {
        switch ($clientSite->type){
            case ClientSite::TYPE_OPENCART:
                $client = new OpencartClient($clientSite->url, $clientSite->auth_key);
                break;
            default: throw new \DomainException('cant find client type');
        }
        $this->client = $client;
    }

    public function getClient(): ClientSiteInterface
    {
        return $this->client;
    }
}
