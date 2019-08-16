<?php

namespace App\Notifications;

use App\Platform;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ThereAreNewLinesToTranslate extends Notification
{
    use Queueable;

    /**
     * @var \App\Platform
     */
    protected $platform;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Platform $platform)
    {
        $this->platform = $platform;
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
            ->subject('There are Lines to Translate')
            ->line('There are new or updated lines that need translation for '.$this->platform->name)
            ->action('Translate', url('/translate'))
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
            'description' => 'There are new or updated lines that need translation for '.$this->platform->name,
            'is_error' => false,
        ];
    }
}
