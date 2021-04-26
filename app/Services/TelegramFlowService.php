<?php

namespace App\Services;

use App\Eloquent\Customer;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

/**
 * Class TelegramFlowService
 * @package App\Services
 * @property Api $telegram
 */
class TelegramFlowService
{
    private $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function processUpdate(Update $update)
    {
        if (!$update->getMessage()) {
            \Log::alert('telegram update has no messages', ['update_id' => $update->getUpdateId()]);
            return;
        }
        $message = $update->getMessage();
        $chat = $message->getChat();
        $chatId = $message->getChat()->getId();
        $text = $message->getText();
        $firstName = $chat->getFirstName();
        $lastName = $chat->getLastName();
        $userName = $chat->getUsername();

        /** @var Customer $customer */
        if (!$customer = Customer::query()->where('chat_id', $chatId)->first()) {
            $customer = $this->addCustomer($chatId, $userName, $firstName, $lastName, Customer::STATE_START);

            $this->telegram->sendMessage([
                'chat_id' => $customer->chat_id,
                'text' => 'Ви успішно підписані на розсилку нових товарів кравлера' . PHP_EOL . 'Гарного дня.',
            ]);
        }

        if ($customer->update_id && $customer->update_id >= $update->getUpdateId()) {
            return;
        }
    }

    /**
     * @param $chatId
     * @param $userName
     * @param $firstName
     * @param $lastName
     * @param $state
     * @return Customer
     */
    private function addCustomer($chatId, $userName, $firstName, $lastName, $state): Customer
    {
        if (!$customer = Customer::query()->where('chat_id', $chatId)->first()) {
            $customer = new Customer();
            $customer->chat_id = $chatId;
            $customer->state = $state;
            $customer->username = $userName;
            $customer->first_name = $firstName;
            $customer->last_name = $lastName;
            $customer->save();
        }

        return $customer;
    }

    public function checkUpdates()
    {
        $updates = $this->telegram->getUpdates();

        foreach ($updates as $update) {
            $this->processUpdate($update);
        }
    }

}
