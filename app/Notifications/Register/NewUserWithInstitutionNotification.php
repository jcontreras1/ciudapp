<?php

namespace App\Notifications\Register;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserWithInstitutionNotification extends Notification
{
    use Queueable;

    public $institution;
    public $user;
    public $token;

    public function __construct(Institution $institution, User $user, $token)
    {
        $this->institution = $institution;
        $this->user = $user;
        $this->token = $token;
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

        $resetPasswordLink = url('/') . '/reset-password/' . $this->token . '?email=' . $this->user->email;
        return (new MailMessage)
                    ->subject('Hemos creado tu cuenta en ' . config('app.name'))
                    ->greeting('Hola, ' . strtoupper($this->user->name))            
                    ->line('Hemos creado tu cuenta para que puedas colaborar con ' . $this->institution->name . '. Pero
                     primero necesitamos que generes una nueva clave mediante el siguiente link: ')
                    ->action('Generar mi clave', $resetPasswordLink)
                    ->line('¡Muchas gracias por unirte!');
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
