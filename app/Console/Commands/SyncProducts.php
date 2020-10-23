<?php

namespace App\Console\Commands;

use App\Eloquent\ClientSite;
use App\Eloquent\Product\Product;
use App\Eloquent\ProductToClient;
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

    protected $signature = 'sync-products {productId?} {clientSiteId?}';
    protected $description = 'Command description';

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(): void
    {
        Log::info('start sync console command. productID:' . $this->argument('productId')
                    . ' clientSiteId:' . $this->argument('clientSiteId'));
        $this->syncUpdatedProducts($this->argument('productId'), $this->argument('clientSiteId'));
        $this->syncCreatedProducts($this->argument('productId'), $this->argument('clientSiteId'));
    }

    private function syncCreatedProducts(?int $productId, ?int $clientSiteId)
    {
        $query = ClientSite::query()
            ->with('vendors')
            ->where('active', true);

        if ($clientSiteId) {
            $query->where('id', $clientSiteId);
        }

        /** @var ClientSite[] $clientSites */
        $clientSites = $query->get()->all();

        foreach ($clientSites as $clientSite) {
            $lastCreatedProducts = $this->getLastCratedProductsByClientSite($clientSite, $productId);
            $client = (new ClientSiteFactory($clientSite))->getClient();

            foreach ($lastCreatedProducts as $product) {
                if ($foreignId = $client->createProduct($product)) {
                    $clientSite->assignProduct($product, $foreignId);
                }
                sleep(self::DELAY_BETWEEN_REQUESTS);
            }
        }
    }

    private function syncUpdatedProducts(?int $productId, ?int $clientSiteId)
    {
        /** @var ClientSite[] $clientSites */
        $query = ClientSite::query()->with('vendors');

        if ($clientSiteId) {
            $query->where('id', $clientSiteId);
        }
        /** @var ClientSite[] $clientSites */
        $clientSites = $query->get()->all();

        foreach ($clientSites as $clientSite) {
            $lastUpdatedProducts = $this->getLastUpdatedProductsByClientSite($clientSite, $productId);
            $client = (new ClientSiteFactory($clientSite))->getClient();

            foreach ($lastUpdatedProducts as $lastUpdatedProduct) {
                $productToClient = $this->getProductToClient($lastUpdatedProduct, $clientSite);
                if ($client->updateProduct($lastUpdatedProduct, $productToClient)) {
                    $clientSite->updateLastSync($lastUpdatedProduct);
                }
                sleep(self::DELAY_BETWEEN_REQUESTS);
            }
        }
    }

    /**
     * @param ClientSite $clientSite
     * @param int|null $productId
     * @return Product[]
     */
    private function getLastCratedProductsByClientSite(ClientSite $clientSite, ?int $productId)
    {
        $query = Product::query()
            ->with(['syncPriceOptions', 'images'])
            ->whereHas('category', function ($query) use ($clientSite) {
                return $query->whereIn('category.vendor_id', $clientSite->getVendorIds());
            })
            ->whereDoesntHave('clientSites', function ($query) use ($clientSite) {
                return $query->where('client_site.id', '=', $clientSite->id);
            })
            ->where('active',1);

        if (!$productId) {
            $query->where('created_at', '>', $this->getLastDayTime());
        } else {
            $query->where('id', $productId);
        }

        return $query->get()->all();
    }

    /**
     * @param int|null $productId
     * @param ClientSite $clientSite
     * @return Product[]
     */
    private function getLastUpdatedProductsByClientSite(ClientSite $clientSite, ?int $productId)
    {
        $query =  Product::query()
            ->with(['syncPriceOptions', 'images'])
            ->whereHas('clientSites', function ($query) use ($clientSite) {
                    return $query->where('client_site.id', '=', $clientSite->id);
                })
            ->where('active',1);

        if (!$productId) {
            $query->where('updated_at', '>', $this->getLastDayTime());
        } else {
            $query->where('id', $productId);
        }

        return $query->get()->all();
    }

    /**
     * @param Product $product
     * @param ClientSite $clientSite
     * @return ProductToClient
     */
    private function getProductToClient(Product $product, ClientSite $clientSite)
    {
        /** @var ProductToClient $productToClient */
        $productToClient = ProductToClient::query()
            ->where('product_id', $product->id)
            ->where('client_site_id', $clientSite->id)
            ->first();
        return $productToClient;
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
