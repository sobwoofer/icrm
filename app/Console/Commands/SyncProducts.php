<?php

namespace App\Console\Commands;

use App\Eloquent\ClientSite;
use App\Eloquent\Product\Product;
use App\Services\ClientSites\ClientSiteFactory;
use Illuminate\Console\Command;
use Log;

/**
 * Class SyncProducts
 * @package App\Console\Commands
 */
class SyncProducts extends Command
{
    private const DELAY_BETWEEN_REQUESTS = 2; //sec

    protected $signature = 'sync-products';
    protected $description = 'Command description';

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(): void
    {
        $this->syncUpdatedProducts();
        $this->syncCreatedProducts();
    }

    private function syncCreatedProducts()
    {
        /** @var ClientSite[] $clientSites */
        $clientSites = ClientSite::query()
            ->with('vendors')
            ->where('active', true)
            ->get()->all();

        foreach ($clientSites as $clientSite) {
            $lastCreatedProducts = $this->getLastCratedProductsByClientSite($clientSite);
            $client = (new ClientSiteFactory($clientSite))->getClient();

            foreach ($lastCreatedProducts as $product) {
                if ($foreignId = $client->createProduct($product)) {
                    $clientSite->assignProduct($product, $foreignId);
                }
                sleep(self::DELAY_BETWEEN_REQUESTS);
            }
        }
    }

    /**
     * @param ClientSite $clientSite
     * @return Product[]
     */
    private function getLastCratedProductsByClientSite(ClientSite $clientSite)
    {
        return Product::query()
            ->with(['syncPriceOptions', 'images'])
            ->whereHas('category', function ($query) use ($clientSite) {
                return $query->whereIn('category.vendor_id', $clientSite->getVendorIds());
            })
            ->whereDoesntHave('clientSites', function ($query) use ($clientSite) {
                return $query->where('client_site.id', '=', $clientSite->id);
            })
            ->where('created_at', '>', $this->getLastDayTime())
            ->where('active',1)
            ->get()->all();
    }

    /**
     * @param ClientSite $clientSite
     * @return Product[]
     */
    private function getLastUpdatedProductsByClientSite(ClientSite $clientSite)
    {
        return Product::query()
            ->with(['syncPriceOptions', 'images'])
            ->whereHas('clientSites', function ($query) use ($clientSite) {
                    return $query->where('client_site.id', '=', $clientSite->id);
                })
            ->where('updated_at', '>', $this->getLastDayTime())
            ->where('active',1)
            ->get()->all();
    }

    private function syncUpdatedProducts()
    {
        /** @var ClientSite[] $clientSites */
        $clientSites = ClientSite::query()->with('vendors')->get()->all();
        foreach ($clientSites as $clientSite) {

            $lastUpdatedProducts = $this->getLastUpdatedProductsByClientSite($clientSite);
            $client = (new ClientSiteFactory($clientSite))->getClient();

            foreach ($lastUpdatedProducts as $lastUpdatedProduct) {
                if ($client->updateProduct($lastUpdatedProduct)) {
                    $clientSite->updateLastSync($lastUpdatedProduct);
                }
                sleep(self::DELAY_BETWEEN_REQUESTS);
            }
        }
    }

    private function getLastDayTime()
    {
        return date('Y-m-d h:i:s', strtotime('-1 day'));
    }

    /**
     * @param string $message
     */
    protected function log($message = '')
    {
        $message = $message . PHP_EOL;
        $this->info($message);
        Log::info($message);
    }

}
