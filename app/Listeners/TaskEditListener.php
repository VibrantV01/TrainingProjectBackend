<?php
namespace App\Listeners;
use App\Events\TaskEditted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;
class TaskEditListener
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
    public function handle(TaskEditted $event)
    {
        $user = $event->user;
        $task = $event->task;
     Mail::send('emails.Taskeditted', ['user' => $user, 'task' => $task], function ($message) use($user) {
        $message->to($user->email, $user->name)->subject
           ('Your Task Editted');
        $message->from('yourDashboard@gmail.com','react-app');
      });
    }
}