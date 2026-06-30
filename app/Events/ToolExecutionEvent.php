<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Broadcasting\Channel;

class ToolExecutionEvent implements ShouldBroadcastNow
{
    public $chatId;
    public $type;
    public $data;

    public function __construct($chatId, $type, $data)
    {
        $this->chatId = $chatId;
        $this->type = $type;
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->chatId);
    }

    public function broadcastAs()
    {
        return 'ToolExecutionEvent';
    }

    public function broadcastWith()
    {
        return [
            'chatId' => $this->chatId,
            'type' => $this->type,
            'data' => $this->data,
        ];
    }
}
