<?php

namespace App\Notifications;

use App\File;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SerializerFailedNotification extends Notification
{
    use Queueable;

    /**
     * @var \App\File
     */
    protected $file;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(File $file, \Exception $exception)
    {
        $this->file = $file;
        $this->exception = $exception;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->line('Serializing the file failed with the following message:')
            ->line($this->exception->getMessage())
            ->action('See All Notifications', url('/notifications'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'file_path' => $this->file->path,
            'file_id' => $this->file->id,
            'exception' => $this->exception->getMessage(),
            'description' => 'Failed attempt to serialize a file: '.$this->exception->getMessage(),
            'is_error' => true,
        ];
    }
}
