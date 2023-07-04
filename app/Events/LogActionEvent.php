<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogActionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $currentDate;
    public $authorId;
    public $description;

    public function __construct($currentDate,$authorId,$description)
    {
        $this->currentDate = $currentDate;
        $this->authorId = $authorId;
        $this->description = $description;
    }
}
