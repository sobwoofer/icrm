<?php

namespace App\Http\Controllers;

use App\Services\FlowService;
use Log;
use Telegram\Bot\Api;

/**
 * Class TelegramController
 * @package App\Http\Controllers
 * @property Api $telegram
 * @property FlowService $flowService
 */
class TelegramController extends Controller
{
    private $flowService;

    public function __construct(Api $telegram, FlowService $flowService)
    {
        $this->telegram = $telegram;
        $this->flowService = $flowService;
    }

    public function index(): void
    {
        $update = $this->telegram->getWebhookUpdates();
        $this->flowService->processUpdate($update);
        Log::info(json_encode($update));
    }
}
