<?php

namespace App\Notifications;

use App\Notifications\Concerns\EnterpriseNotifiableNotification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminManualAlert extends EnterpriseNotifiableNotification
{
    public function __construct(
        public readonly string $subjectLine,
        public readonly string $bodyMessage,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subjectLine)
            ->greeting(__('Admin Alert'))
            ->line($this->bodyMessage);
    }
}
