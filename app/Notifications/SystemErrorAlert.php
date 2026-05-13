<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\SlackMessage;

class SystemErrorAlert extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

        public function toSlack($notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->to('#errors')
            ->error()
            ->attachment(function ($attachment) {

                $attachment
                    ->title('🚨 System Error')
                    ->color('danger')
                    ->fields([
                        'Exception' => $this->exception::class,
                        'Message' => $this->exception->getMessage(),
                        'File' => $this->exception->getFile(),
                        'Line' => $this->exception->getLine(),
                        'URL' => request()->fullUrl(),
                    ]);
            });
    }
}
