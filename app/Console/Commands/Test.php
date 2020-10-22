<?php

namespace App\Console\Commands;


use App\Eloquent\ForeignOption;
use App\Eloquent\Product\PriceOption;
use App\Events\CreatedProduct;
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

//        $priceOptions = PriceOption::query()->get()->all();
//
//        /** @var PriceOption $priceOption */
//        foreach ($priceOptions as $priceOption) {
//            $priceOption->foreign_option_id = $this->resolveForeignOptionIdByName($priceOption->name);
//            $priceOption->save();
//            $this->info('done option id: ' . $priceOption->id . PHP_EOL);
//        }

//        $priceOption = PriceOption::query()->where('id',4126)->get()->first();
//
//            $priceOption->foreign_option_id = $this->resolveForeignOptionIdByName($priceOption->name);
//            $priceOption->save();
//            $this->info('done option id: ' . $priceOption->id . PHP_EOL);




//        event(new CreatedProduct('matras test', 'url test', '33'));
        $productLink  = 'https://matroluxe.com/ru/matras-topper-futon-5';
        $this->matroluxCrawler->crawlProductByUrl($productLink, 22);


//        $this->runBot();
//        while (true) {
//            $this->runBot();
//            sleep(2);
//        }

    }

    private function resolveForeignOptionIdByName(string $priceOptionName): ?int
    {
        preg_match_all('!\d+!', $priceOptionName, $matches);

        if (!empty($matches[0]) && count($matches[0]) === 2) {
            $firstSize = (int)$matches[0][0];
            $secondSize = (int)$matches[0][1];

            $sizeCombinations = [
                $firstSize . 'x' . $secondSize,
                $secondSize . 'x' . $firstSize,
                $firstSize * 10 . 'x' . $secondSize * 10,
                $secondSize * 10 . 'x' . $firstSize * 10,
            ];

            return ForeignOption::query()->whereIn('name', $sizeCombinations)->pluck('id')->first();
        }

        return null;
    }

}
