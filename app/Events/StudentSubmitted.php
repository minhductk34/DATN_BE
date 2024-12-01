<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $student;

    public function __construct($roomId, $student)
    {
        $this->roomId = $roomId;
        $this->student = $student;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('presence-room.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return "student.submitted";
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->student->idcode,
            'name' => $this->student->name,
        ];
    }
}
