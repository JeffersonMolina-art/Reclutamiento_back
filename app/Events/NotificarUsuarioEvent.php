<?php

namespace App\Events;

use App\Models\User;
use App\Notifications\NotificacionSistema;

class NotificarUsuarioEvent extends AppBaseEvent
{

    /**
     * Crea una nueva instancia del evento.
     */
    public function __construct(string $id_usuario_notificar, $data)
    {

        parent::__construct(
            channel: "notificaciones.{$id_usuario_notificar}",  // Canal único por usuario
            event: "mostrar.notificacion.usuario", // Nombre del evento
            data: $data
        );
    }



}
