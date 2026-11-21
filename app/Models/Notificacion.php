<?php

namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;

/**
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property string $data
 * @property string|null $read_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder<static>|Notificacion newModelQuery()
 * @method static Builder<static>|Notificacion newQuery()
 * @method static Builder<static>|Notificacion onlyTrashed()
 * @method static Builder<static>|Notificacion query()
 * @method static Builder<static>|Notificacion whereCreatedAt($value)
 * @method static Builder<static>|Notificacion whereData($value)
 * @method static Builder<static>|Notificacion whereDeletedAt($value)
 * @method static Builder<static>|Notificacion whereId($value)
 * @method static Builder<static>|Notificacion whereNotifiableId($value)
 * @method static Builder<static>|Notificacion whereNotifiableType($value)
 * @method static Builder<static>|Notificacion whereReadAt($value)
 * @method static Builder<static>|Notificacion whereType($value)
 * @method static Builder<static>|Notificacion whereUpdatedAt($value)
 * @method static Builder<static>|Notificacion withTrashed()
 * @method static Builder<static>|Notificacion withoutTrashed()
 * @property-read Model $notifiable
 * @method static DatabaseNotificationCollection<int, static> all($columns = ['*'])
 * @method static DatabaseNotificationCollection<int, static> get($columns = ['*'])
 * @method static Builder<static>|Notificacion read()
 * @method static Builder<static>|Notificacion unread()
 * @mixin Eloquent
 */
class Notificacion extends BaseDatabaseNotification
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.notifications';


}

