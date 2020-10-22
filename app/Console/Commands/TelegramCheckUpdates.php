<?php

namespace App\Console\Commands;

use App\Services\TelegramFlowService;
use Illuminate\Console\Command;
use Telegram\Bot\Api;

/**
 * Class Test
 * @package App\Console\Commands
 * @property Api $telegram
 * @property TelegramFlowService $flowService
 */
class TelegramCheckUpdates extends Command
{
    protected $signature = 'telegram-check-updates';
    protected $description = 'Command description';

    private $telegram;
    private $flowService;

    public function __construct(Api $telegram, TelegramFlowService $flowService)
    {
        $this->telegram = $telegram;
        $this->flowService = $flowService;
        parent::__construct();
    }

    /**
     * @return string|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        $updates = $this->telegram->getUpdates();

        foreach ($updates as $update) {
            $this->flowService->processUpdate($update);
        }
    }

}
