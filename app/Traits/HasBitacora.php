<?php


namespace App\Traits;


use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Permission\Contracts\Role;
use function get_class;

trait HasBitacora
{

    /**
     * Set the polymorphic relation.
     *
     * @return MorphMany
     */
    public function bitacoras(): MorphMany
    {
        return $this->morphMany(Bitacora::class, 'model');
    }


    /**
     * Agrega un registro en la bitácora.
     *
     * @param string $setion    Nombre de la sección donde se registra la bitácora.
     * @param string $titulo    Título o resumen del evento.
     * @param string $comentario Comentario o detalle adicional del evento.
     * @param string|null $icono Nombre del icono (ej. 'tabler-message').
     * @param string|null $color Color de UI (ej. 'primary', 'success').
     * @param User|null $user Usuario relacionado (opcional).
     *
     * @return void
     */
    public function addBitacora($setion,$titulo,$comentario, string $icono = null, string $color = null,$user=null)
    {
        $bitacora = new Bitacora([
            'seccion' => $setion,
            'titulo' => $titulo,
            'comentario' => $comentario,
            'icono' => $icono,
            'color' => $color,
            'usuario_id' => $user ?? request()->user()->id ?? null,
        ]);

        $model = $this->getModel();

        if ($model->exists) {
            $this->bitacoras()->save($bitacora);
            $model->load('bitacoras');
        } else {
            $class = get_class($model);

            $class::saved(
                function ($object) use ($bitacora, $model) {
                    static $modelLastFiredOn;
                    if ($modelLastFiredOn !== null && $modelLastFiredOn === $model) {
                        return;
                    }
                    $object->bitacoras()->sync($bitacora, false);
                    $object->load('bitacoras');
                    $modelLastFiredOn = $object;
                });
        }

        return $bitacora;
    }
    /**
     * Revoke the given role from the model.
     *
     * @param string|Role $role
     */
    public function deleteBitacora($bitacora)
    {
        $this->bitacoras()->delete($bitacora);

        $this->load('bitacoras');

        return $bitacora;
    }
    public function generarComentarioCreacion(): string
    {
        $datos = $this->obtenerAtributosBitacora($this->getAttributes());

        if (empty($datos)) {
            return 'Se creó el registro.';
        }

        $partes = [];

        foreach ($datos as $campo => $valor) {
            $etiqueta = $this->obtenerEtiquetaBitacora($campo);
            $valor = $this->transformarValorBitacora($campo, $valor);

            $partes[] = "{$etiqueta}: '{$this->formatearValorBitacora($valor)}'";
        }

        return 'Se creó el registro con los valores: ' . implode(' | ', $partes);
    }

    public function generarComentarioActualizacion(): string
    {
        $lineas = [];

        foreach ($this->obtenerAtributosBitacora($this->getDirty()) as $campo => $_) {
            $etiqueta = $this->obtenerEtiquetaBitacora($campo);

            $anterior = $this->formatearValorBitacora(
                $this->transformarValorBitacora($campo, $this->getOriginal($campo))
            );

            $nuevo = $this->formatearValorBitacora(
                $this->transformarValorBitacora($campo, $this->getAttribute($campo))
            );

            $lineas[] = "{$etiqueta} cambió de «{$anterior}» a «{$nuevo}».";
        }

        return implode("\n", $lineas);
    }

    public function generarComentarioEliminacion(): string
    {
        $datos = $this->obtenerAtributosBitacora($this->getAttributes());

        if (empty($datos)) {
            return 'Se eliminó el registro.';
        }

        $partes = [];

        foreach ($datos as $campo => $valor) {
            $etiqueta = $this->obtenerEtiquetaBitacora($campo);
            $valor = $this->transformarValorBitacora($campo, $valor);

            $partes[] = "{$etiqueta}: '{$this->formatearValorBitacora($valor)}'";
        }

        return 'Se eliminó el registro que tenía los valores: ' . implode(' | ', $partes);
    }

    protected function obtenerAtributosBitacora(array $atributos): array
    {
        $excepto = $this->obtenerCamposExcluidosBitacora();
        $resultado = [];

        foreach ($atributos as $campo => $valor) {
            if (in_array($campo, $excepto, true)) {
                continue;
            }

            $resultado[$campo] = $valor;
        }

        return $resultado;
    }

    protected function transformarValorBitacora(string $campo, $valor)
    {
        if (in_array($campo, $this->obtenerCamposOcultosBitacora(), true)) {
            return '[PROTEGIDO]';
        }

        $relaciones = $this->obtenerRelacionesBitacora();

        if (array_key_exists($campo, $relaciones)) {
            return $this->resolverNombreRelacionBitacora($relaciones[$campo], $valor);
        }

        $booleanos = $this->obtenerBooleanosBitacora();

        if (array_key_exists($campo, $booleanos)) {
            $config = $booleanos[$campo];

            if (is_array($config) && count($config) === 2) {
                if (is_null($valor)) {
                    return 'N/A';
                }

                return $valor ? $config[0] : $config[1];
            }

            if (is_null($valor)) {
                return 'N/A';
            }

            return $valor ? 'Sí' : 'No';
        }

        if ($valor instanceof \Carbon\CarbonInterface) {
            return $valor->format('d/m/Y');
        }

        return $valor;
    }

    protected function formatearValorBitacora($valor): string
    {
        if ($valor === null || $valor === '') {
            return 'Sin definir';
        }

        if (is_bool($valor)) {
            return $valor ? 'Sí' : 'No';
        }

        if (is_array($valor) || is_object($valor)) {
            return json_encode($valor, JSON_UNESCAPED_UNICODE);
        }

        return (string) $valor;
    }

    protected function obtenerEtiquetaBitacora(string $campo): string
    {
        $etiquetas = $this->obtenerEtiquetasBitacora();

        if (array_key_exists($campo, $etiquetas)) {
            return $etiquetas[$campo];
        }

        return ucfirst(str_replace('_', ' ', $campo));
    }

    protected function obtenerCamposExcluidosBitacora(): array
    {
        $default = ['created_at', 'updated_at', 'deleted_at'];

        $modelo = property_exists($this, 'bitacoraExcepto')
            ? (array) $this->bitacoraExcepto
            : [];

        return array_values(array_unique(array_merge($default, $modelo)));
    }

    protected function obtenerCamposOcultosBitacora(): array
    {
        return property_exists($this, 'bitacoraOcultos')
            ? (array) $this->bitacoraOcultos
            : [];
    }

    protected function obtenerEtiquetasBitacora(): array
    {
        return property_exists($this, 'bitacoraEtiquetas')
            ? (array) $this->bitacoraEtiquetas
            : [];
    }

    /**
     * Reemplaza un id por el valor legible del registro relacionado.
     *
     * @param array{0: string, 1: string} $config [método de la relación, atributo a mostrar]
     */
    protected function resolverNombreRelacionBitacora(array $config, $id)
    {
        if ($id === null || $id === '') {
            return null;
        }

        [$relacion, $atributo] = $config;

        $modelo = $this->{$relacion}()->getRelated()->newQuery();

        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelo->getModel()), true)) {
            $modelo->withTrashed();
        }

        return $modelo->find($id)?->{$atributo} ?? $id;
    }

    protected function obtenerRelacionesBitacora(): array
    {
        return property_exists($this, 'bitacoraRelaciones')
            ? (array) $this->bitacoraRelaciones
            : [];
    }

    protected function obtenerBooleanosBitacora(): array
    {
        return property_exists($this, 'bitacoraBooleanos')
            ? (array) $this->bitacoraBooleanos
            : [];
    }
}
