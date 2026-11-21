<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasScopedAppendsTrait
{
    /**
     * Bandera para saber si la instancia fue modificada por un scope.
     */
    protected bool $appendsModificadosEnInstancia = false;
    /**
     * Array con la lista final de appends para esta instancia.
     */
    protected array $appendsPersonalizadosInstancia = [];
    /**
     * Sobrescribe el método de Eloquent que decide qué appends serializar.
     */
    protected function getArrayableAppends(): array
    {
        if ($this->appendsModificadosEnInstancia) {
            return $this->appendsPersonalizadosInstancia;
        }

        return parent::getArrayableAppends();
    }
    /**
     * Scope para remover todos los appends o conservar únicamente los indicados.
     *
     * Invocación:
     * ->sinAtributoAdicional()
     * ->sinAtributoAdicional('foto')
     * ->sinAtributoAdicional(['foto', 'texto'])
     */
    public function scopeSinAtributoAdicional(Builder $query, string|array ...$conservar): Builder
    {
        $atributosNormalizados = $this->normalizarAtributos($conservar);

        $query->afterQuery(function ($result) use ($atributosNormalizados) {
            $this->procesarResultado($result, function (Model $model) use ($atributosNormalizados) {
                $appendsFinales = empty($atributosNormalizados)
                    ? []
                    : array_intersect($model->getAppends(), $atributosNormalizados);

                $model->aplicarCambiosDeAppends($appendsFinales);
            });

            return $result;
        });

        return $query;
    }
    /**
     * Scope para agregar atributos adicionales dinámicamente.
     *
     * Invocación:
     * ->conAtributoAdicional('detalle_activo')
     * ->conAtributoAdicional('detalle_activo', 'usuario_create')
     * ->conAtributoAdicional(['detalle_activo', 'usuario_create', 'tipo_plantilla'])
     */
    public function scopeConAtributoAdicional(Builder $query, string|array ...$atributos): Builder
    {
        $atributosNormalizados = $this->normalizarAtributos($atributos);

        $query->afterQuery(function ($result) use ($atributosNormalizados) {
            $this->procesarResultado($result, function (Model $model) use ($atributosNormalizados) {
                $appendsFinales = array_unique(
                    array_merge($model->getAppends(), $atributosNormalizados)
                );

                $model->aplicarCambiosDeAppends($appendsFinales);
            });

            return $result;
        });

        return $query;
    }
    /**
     * Marca el modelo como modificado y establece su lista de appends aislada.
     */
    public function aplicarCambiosDeAppends(array $appends = []): static
    {
        $this->appendsModificadosEnInstancia = true;
        $this->appendsPersonalizadosInstancia = array_values($appends);

        return $this;
    }

    /**
     * Convierte cualquier combinación de arrays/strings recibidos en un array plano de strings.
     */
    private function normalizarAtributos(array $atributos): array
    {
        $resultado = [];
        foreach ($atributos as $item) {
            if (is_array($item)) {
                $resultado = array_merge($resultado, $item);
            } elseif (is_string($item)) {
                $resultado[] = $item;
            }
        }

        return array_unique($resultado);
    }
    /**
     * Helper privado para procesar tanto Colecciones como Modelos individuales.
     */
    private function procesarResultado($result, callable $callback): void
    {
        if ($result instanceof Collection) {
            $result->each($callback);
        } elseif ($result instanceof Model) {
            $callback($result);
        }
    }
}
