<?php

namespace App\Console\Commands;


use App\Services\Crawlers\ComeforCrawler;
use App\Services\Crawlers\EmmCrawler;
use App\Services\Crawlers\MatroluxCrawler;
use Illuminate\Console\Command;
use Telegram\Bot\Api;

/**
 * Class Test
 * @package App\Console\Commands
 * @property ComeforCrawler $comeforCrawler
 * @property EmmCrawler $emmCrawler
 * @property MatroluxCrawler $matroluxCrawler
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

    private $comeforCrawler;
    private $emmCrawler;
    private $matroluxCrawler;

    public function __construct(
        ComeforCrawler $comeforCrawler,
        EmmCrawler $emmCrawler,
        MatroluxCrawler $matroluxCrawler,
        Api $telegram
    )
    {
        $this->comeforCrawler = $comeforCrawler;
        $this->emmCrawler = $emmCrawler;
        $this->matroluxCrawler = $matroluxCrawler;
        $this->telegram = $telegram;
        parent::__construct();
    }

    /**
     * @return string|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        $productLink  = 'https://matroluxe.com/ru/matras-topper-futon-5';
        $this->matroluxCrawler->crawlProductByUrl($productLink, 22);


//        $this->runBot();
//        while (true) {
//            $this->runBot();
//            sleep(2);
//        }

    }

}
