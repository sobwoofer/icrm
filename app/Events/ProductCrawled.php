<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class FreshItemsFound
 * @package App\Events
 * @property string $name;
 * @property string $description;
 * @property string $url;
 * @property string $imageUrl;
 * @property string $article;
 * @property float $price;
 * @property array $images;
 * @property array $priceOptions
 * @property int $categoryId
 */
class ProductCrawled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $name;
    public $description;
    public $url;
    public $imageUrl;
    public $article;
    public $price;
    public $images;
    public $priceOptions;
    public $categoryId;

    public function __construct(
        string $name,
        string $description,
        string $url,
        string $imageUrl,
        string $article,
        float $price,
        array $images,
        array $priceOptions,
        int $categoryId
    )
    {
        $this->name = $name;
        $this->description = $description;
        $this->url = $url;
        $this->imageUrl = $imageUrl;
        $this->article = $article;
        $this->price = $price;
        $this->images = $images;
        $this->priceOptions = $priceOptions;
        $this->categoryId = $categoryId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
