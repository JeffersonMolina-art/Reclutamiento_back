<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Notifications\Notification;

class NotificacionBase extends Notification
{
    use Queueable;


    public $titulo;
    public $cuerpo;
    public $imagen;
    public $url;
    public $saludo;
    public $agradecimiento;
    public $subject;

    /**
     * @param $titulo
     * @param $cuerpo
     * @param $imagen
     * @param $url
     * @param $saludo
     * @param $agradecimiento
     * @param $subject
     */
    public function __construct($titulo=null,$cuerpo=null, $imagen=null, $url=null,$saludo = null,$agradecimiento = null,$subject = null)
    {
        $this->titulo = $titulo ?? "Titulo";
        $this->cuerpo = $cuerpo ?? "Cuerpo de la notificación";
        $this->imagen = $imagen ?? getLogo();
        $this->url = $url ?? env('APP_URL');
        $this->saludo = $saludo ?? "Estimado";
        $this->agradecimiento = $agradecimiento ?? 'Saludos cordiales';
        $this->subject = $subject ?? "Notificación SIGUA";
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

//        $vist = Markdown::parse(view('home')->render());

        return (new MailMessage)
                    ->greeting($this->saludo)
                    ->line($this->titulo)
                    ->line($this->cuerpo)
//                    ->action('Revisar', $this->url)
                    ->salutation($this->agradecimiento)
                    ->subject($this->subject);


    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            "titulo" => $this->titulo ?? "Hola ".$notifiable->name." esta es una prueba de notificación",
            "texto" => $this->cuerpo ?? "Hola ".$notifiable->name." esta es una prueba de notificación",
            "imagen" => $this->imagen,
            "url" => $this->url
        ];
    }
}
