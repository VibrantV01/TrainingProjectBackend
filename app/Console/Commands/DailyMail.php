<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;


class DailyMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:daily';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail daily to all the users about their pending tasks';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all();
        foreach ($users as $user) {
            
            
            $task_pending = Task::where([['asigned_to', '=', $user->id],['deleted_by','=',null ]]);
            Mail::raw("Please visit your dashboard", function ($mail) use ($user) {
                $mail->from('digamber@positronx.com');
                $mail->to($user->email)
                    ->subject('Daily New Quote!');
            });
        
        }
        $this->info('Successfully sent daily quote to everyone.');

    }
}