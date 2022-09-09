<?php
namespace App\Listeners;
use App\Events\TaskAssigned;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;
class TaskAssignedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    /**
     * Handle the event.
     *
     * @param  TaskAssigned  $event
     * @return void
     */
    public function handle(TaskAssigned $event)
    {
        $user = $event->user;
        $task = $event->task;
     Mail::send('emails.newTask', ['user' => $user, 'task' => $task], function ($message) use($user) {
        $message->to($user->email, $user->name)->subject
           ('Urgent::New Task Added');
        $message->from('yourDashboard@gmail.com','react-app');
      });
    }
}