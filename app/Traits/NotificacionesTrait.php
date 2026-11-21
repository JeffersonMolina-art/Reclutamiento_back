<?php

namespace App\Traits;

use App\Events\NotificarUsuarioEvent;
use App\Models\Notificacion;
use App\Notifications\NotificacionSistema;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\RoutesNotifications;

trait NotificacionesTrait
{
    use RoutesNotifications;

    /**
     * Enviar notificación en tiempo real al usuario.
     *
     * @param string $titulo
     * @param string $mensaje
     */
    public function enviarNotificacionRealTime($titulo, $mensaje): void
    {

        $this->notify(new NotificacionSistema($titulo, $mensaje));

        $notificaciones = $this->notifications()->first();

        event(new NotificarUsuarioEvent($this->id, $notificaciones->toArray()));

    }

    /**
     * Get the entity's notifications.
     *
     * @return MorphMany<DatabaseNotification, $this>
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notificacion::class, 'notifiable')->latest();
    }

    /**
     * Get the entity's read notifications.
     *
     * @return Builder
     */
    public function notificacionesLeidas(): MorphMany
    {
        return $this->notifications()->read();
    }

    /**
     * Get the entity's unread notifications.
     *
     * @return Builder
     */
    public function notificacionesSinLeer(): MorphMany
    {
        return $this->notifications()->unread();
    }

}
