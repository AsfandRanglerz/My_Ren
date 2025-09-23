<?php

namespace App\Jobs;

use App\Helpers\NotificationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class JobNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $fcm;

    protected $title;

    protected $description;

    protected $data;

    public function __construct($fcm, $title, $description, $data)
    {
        $this->fcm = $fcm;
        $this->title = $title;
        $this->description = $description;
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
            NotificationHelper::sendFcmNotification($this->fcm, $this->title, $this->description, $this->data);
        } catch (\Exception $exception) {
            // Handle exceptions like logging the failure
            Log::error('FCM send error: '.$exception->getMessage());
        }
    }
}
