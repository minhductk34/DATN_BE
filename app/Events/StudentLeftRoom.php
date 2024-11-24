<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentLeftRoom implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $student;

    public function __construct($room, $student)
    {
        $this->room = $room;
        $this->student = $student;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('room.' . $this->room->id);
    }
}

