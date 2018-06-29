<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendReminderEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $email;
    protected $code;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$code)
    {
        //
        $this->email=$email;
        $this->code=$code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Mail::raw($this->code,function($message){
            $message->to($this->email);
        });
    }
}
