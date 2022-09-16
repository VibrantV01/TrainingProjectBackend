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
class UserEdit extends Event 
{
    use  SerializesModels;
    public $user;
    public $task;
    public function __construct($user)
    {
        $this->user=$user;
    }

}