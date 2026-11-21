<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificacionSistema extends Notification
{
    use Queueable;

    protected $titulo;
    protected $mensaje;
    /**
     * Create a new notification instance.
     */
    public function __construct(string $titulo, string $mensaje)
    {
        $this->titulo = $titulo;
        $this->mensaje = $mensaje;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */

    public function toDatabase($notifiable)
    {
        return [
            'titulo' => $this->titulo, // Se guardará en 'data'
            'mensaje' => $this->mensaje,
//            'url' => 'https://www.ejemplo.com',
//            'imagen' => 'https://via.placeholder.com/150'
        ];
    }
}
