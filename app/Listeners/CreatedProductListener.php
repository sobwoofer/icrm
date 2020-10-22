<?php

namespace App\Listeners;

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

        $customers = Customer::all();
        $message = 'Завтра на сайті з\'явиться новий товар: ' . PHP_EOL;
        $message .= 'Ім\'я: ' . $event->name. PHP_EOL;
        $message .= 'Ціна: ' . $event->price. PHP_EOL;
        $message .= 'Посилання на сайт виробника: ' . $event->url. PHP_EOL;


        //send message for all bot customers
        foreach ($customers as $customer) {
            $this->telegram->sendMessage([
                'chat_id' => $customer->chat_id,
                'text' => $message,
            ]);
        }
    }
}
