<?php

namespace App\Jobs;

use App\NotificationType;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AttachUsersToNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\NotificationType
     */
    protected $notificationType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(NotificationType $notificationType)
    {
        $this->notificationType = $notificationType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        User::chunk(200, function ($users) {
            $this->notificationType->users()->syncWithoutDetaching(
                $users->pluck('id')->toArray()
            );
        });
    }
}
