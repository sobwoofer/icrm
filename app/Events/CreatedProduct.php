<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class FreshItemsFound
 * @package App\Events
 * @property string $name
 * @property string $url
 * @property float $price
 */
class CreatedProduct
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $name;
    public $url;
    public $price;

    public function __construct(string $name, string $url, float $price)
    {
        $this->name = $name;
        $this->url = $url;
        $this->price = $price;
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
