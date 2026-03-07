<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\User\Emails\EmailUserVerifyRegister;

class RegisterUserEmailConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $actionUrl = route('auth.email.confirm', [
                'id' => $this->data->id,
                'code' => $this->data->confirmation_code
            ]);
	        Mail::to($this->data->email)->send(new EmailUserVerifyRegister($this->data, $actionUrl));
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }
}
