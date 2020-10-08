<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Telegram\Bot\Api;

/**
 * Class Test
 * @package App\Console\Commands
 * @property Api $telegram
 */
class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $telegram;


    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
        parent::__construct();
    }

    /**
     * @return string|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        $this->telegram->removeWebhook();

//        $this->runBot();
//        while (true) {
//            $this->runBot();
//            sleep(2);
//        }

    }

}
