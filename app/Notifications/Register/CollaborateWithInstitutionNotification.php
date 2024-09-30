<?php

namespace App\Notifications\Register;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollaborateWithInstitutionNotification extends Notification
{
    use Queueable;


    public $institution;
    public $user;

    public function __construct(Institution $institution, User $user)
    {
        $this->institution = $institution;
        $this->user = $user;
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
        $urlInstitucion = route('institution.edit', $this->institution->id);
        return (new MailMessage)
                    ->subject('['.config('app.name').'] - ¡Te invitaron a colaborar!')
                    ->greeting('Hola, ' . strtoupper($this->user->name))            
                    ->line('Te invitaron a colaborar con: ' . $this->institution->name)
                    ->action('Entrar a gestionar', $urlInstitucion)
                    ->line('Si considerás que se trata de un error, desestimá este correo.');
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
}
