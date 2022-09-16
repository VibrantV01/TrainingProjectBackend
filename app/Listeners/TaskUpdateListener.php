<?php
namespace App\Listeners;
use App\Events\TaskUpdate;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;
class TaskUpdateListener
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
    public function handle(TaskUpdate $event)
    {
        $user = $event->user;
        $task = $event->task;
     Mail::send('emails.Taskupdatted', ['user' => $user, 'task' => $task], function ($message) use($user) {
        $message->to($user->email, $user->name)->subject
           ('Task Status Updated');
        $message->from('yourDashboard@gmail.com','react-app');
      });
    }
}