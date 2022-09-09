<?php
namespace App\Events;
use App\Models\Task;  // changed here
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
                                    
//use Illuminate\Foundation\Events\Dispatchable; 
// not available in lumen
use Illuminate\Foundation\Events\Dispatchable;

use Illuminate\Broadcasting\InteractsWithSockets;

//{/*implements ShouldBroadcast*/}
class TaskAssigned extends Event implements ShouldBroadcast
{
    use  SerializesModels;
    public $user;
    public $task;
    public function __construct($user, $task)
    {
        $this->user=$user;
        $this->task = $task;
    }
    public function broadcastOn()
  {
      return ['7e4f96f1381b51749e6a'];
  }
    // public function broadcastAs()
    // {
    //     return 'my-event';
    // }
}