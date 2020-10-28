<?php

namespace App\Listeners;

use App\Eloquent\ClientSite;
use App\Eloquent\Customer;
use App\Events\CreatedProduct;
use App\Services\TelegramFlowService;
use Telegram\Bot\Api;

/**
 * Class CreatedOrderListener
 * @package App\Listeners
 * @property Api $telegram
 * @property TelegramFlowService $flowService
 */
class CreatedProductListener
{
    private $telegram;
    private $flowService;

    public function __construct(Api $telegram, TelegramFlowService $flowService)
    {
        $this->telegram = $telegram;
        $this->flowService = $flowService;
    }

    /**
     * @param CreatedProduct $event
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(CreatedProduct $event)
    {
        $this->flowService->checkUpdates(); //create customers if not exist

        /** @var ClientSite[] $clientSites */
        $clientSites = ClientSite::query()
            ->leftJoin('vendor_to_client', 'client_site.id', '=',  'vendor_to_client.client_site_id')
            ->where('vendor_to_client.vendor_id', $event->product->category->vendor_id)
            ->get()->all();


        $sitesMessage = '';
        foreach ($clientSites as $clientSite) {
            $sitesMessage .=  ' ' . $clientSite->url;
        }

        $customers = Customer::all();
        $message = 'Незабаром на ' . $sitesMessage . ' ';
        $message .= 'з\'явиться новий товар: ' . PHP_EOL;
        $message .= 'Ціна: ' . $event->product->price. PHP_EOL;
        $message .= 'Ім\'я: ' . $event->product->name. PHP_EOL;
        $message .= 'Посилання на сайт виробника: ' . $event->product->url. PHP_EOL;


        //send message for all bot customers
        foreach ($customers as $customer) {
            $this->telegram->sendMessage([
                'chat_id' => $customer->chat_id,
                'text' => $message,
            ]);
        }
    }
}
