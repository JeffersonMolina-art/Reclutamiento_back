<?php

    namespace App\Models;


    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Support\Carbon;

    /**
     * @property int $id
     * @property string|null $codigo
     * @property string $nombre
     * @property string|null $emails
     * @property string $estado
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property int|null $regiones_id
     * @property int|null $gerencia_id
     * @property int|null $jefe_id
     * @property int|null $auxiliar_id
     * @property int|null $auxiliar2_id
     * @property string|null $email
     * @property int|null $municipio_id
     * @property int|null $departamento_id
     * @property string|null $latitud
     * @property string|null $longitud
     * @property string|null $id_place_google
     * @property int|null $codigo_aprobador
     * @property string|null $grupo_aprobador
     * @property string|null $codigo_area_t24
     * @property-read Gerencia|null $gerencia
     * @property-read Region|null $region
     * @property-read User|null $user
     * @method static Builder<static>|Unidad newModelQuery()
     * @method static Builder<static>|Unidad newQuery()
     * @method static Builder<static>|Unidad query()
     * @method static Builder<static>|Unidad whereAuxiliar2Id($value)
     * @method static Builder<static>|Unidad whereAuxiliarId($value)
     * @method static Builder<static>|Unidad whereCodigo($value)
     * @method static Builder<static>|Unidad whereCodigoAprobador($value)
     * @method static Builder<static>|Unidad whereCodigoAreaT24($value)
     * @method static Builder<static>|Unidad whereCreatedAt($value)
     * @method static Builder<static>|Unidad whereDepartamentoId($value)
     * @method static Builder<static>|Unidad whereEmail($value)
     * @method static Builder<static>|Unidad whereEmails($value)
     * @method static Builder<static>|Unidad whereEstado($value)
     * @method static Builder<static>|Unidad whereGerenciaId($value)
     * @method static Builder<static>|Unidad whereGrupoAprobador($value)
     * @method static Builder<static>|Unidad whereId($value)
     * @method static Builder<static>|Unidad whereIdPlaceGoogle($value)
     * @method static Builder<static>|Unidad whereJefeId($value)
     * @method static Builder<static>|Unidad whereLatitud($value)
     * @method static Builder<static>|Unidad whereLongitud($value)
     * @method static Builder<static>|Unidad whereMunicipioId($value)
     * @method static Builder<static>|Unidad whereNombre($value)
     * @method static Builder<static>|Unidad whereRegionesId($value)
     * @method static Builder<static>|Unidad whereUpdatedAt($value)
     * @property-read User|null $auxiliar
     * @property-read User|null $auxiliar2
     * @property-read Departamento|null $departamento
     * @property-read User|null $jefe
     * @property-read Municipio|null $municipio
     * @mixin Eloquent
     */
    class Unidad extends Model
    {


        use SoftDeletes;

        protected $table = 'reclutamiento.unidads';

        protected $fillable = [
            'codigo',
            'nombre',
            'emails',
            'estado',
            'regiones_id',
            'gerencia_id',
            'jefe_id',
            'auxiliar_id',
            'email',
            'municipio_id',
            'departamento_id',
            'latitud',
            'longitud',
        ];


        /**
         * Validation rules
         *
         * @var array
         */
        public static $rules = [
            'codigo' => 'nullable|string|max:382',
            'nombre' => 'required|string|max:382',
            'emails' => 'nullable|string',
            'estado' => 'required|string|max:510',
            'regiones_id' => 'nullable|integer',
            'gerencia_id' => 'nullable|integer',
            'jefe_id' => 'nullable|integer',
            'auxiliar_id' => 'nullable|integer',
            'email' => 'nullable|string|max:382',
            'municipio_id' => 'nullable|integer',
            'departamento_id' => 'nullable|integer',
            'latitud' => 'nullable|string|max:382',
            'longitud' => 'nullable|string|max:382',
        ];


        /**
         * Custom messages for validation
         *
         * @var array
         */
        public static $messages = [

        ];


        /**
         * Accessor for relationships
         *
         * @var array
         */
        public function gerencia()
        {
            return $this->belongsTo(Gerencia::class, 'gerencia_id', 'id');
        }

        public function user()
        {
            return $this->belongsTo(User::class, 'jefe_id', 'id');
        }

        public function jefe()
        {
            return $this->belongsTo(User::class, 'jefe_id', 'id');
        }

        public function auxiliar()
        {
            return $this->belongsTo(User::class, 'auxiliar_id', 'id');
        }

        public function region()
        {
            return $this->belongsTo(Region::class, 'regiones_id', 'id');
        }

        public function municipio()
        {
            return $this->belongsTo(Municipio::class, 'municipio_id', 'id');
        }

        public function departamento()
        {
            return $this->belongsTo(Departamento::class, 'departamento_id', 'id');
        }

    }
