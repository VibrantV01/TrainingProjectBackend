<?php
namespace App\Listeners;
use App\Events\UserEdit;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;
class UserEditListener
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
    public function handle(UserEdit $event)
    {
        $user = $event->user;
        // $task = $event->task;
     Mail::send('emails.UserUpdate', ['user' => $user], function ($message) use($user) {
        $message->to($user->email, $user->name)->subject
           ('Your profile editted');
        $message->from('yourDashboard@gmail.com','react-app');
      });
    }
}