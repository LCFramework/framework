<?php

namespace LCFramework\Framework\LastChaos\Eloquent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PendingDeletionScope implements Scope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected array $extensions = [
        'Restore',
        'WithPendingDeletes',
        'WithoutPendingDeletes',
        'OnlyPendingDeletes',
    ];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where(
            $model->getQualifiedPendingDeleteColumn(),
            '=',
            0
        );
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder): void
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }

        $builder->onDelete(function (Builder $builder) {
            $column = $this->getPendingDeleteColumn($builder);

            return $builder->update([
                $column => $builder->getModel()->freshTimestamp()->addDays()->unix(),
            ]);
        });
    }

    /**
     * Get the "pending delete" column for the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return string
     */
    protected function getPendingDeleteColumn(Builder $builder): string
    {
        if (count((array) $builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedPendingDeleteColumn();
        }

        return $builder->getModel()->getPendingDeleteColumn();
    }

    /**
     * Add the restore extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addRestore(Builder $builder): void
    {
        $builder->macro('restore', function (Builder $builder) {
            $builder->withPendingDeletes();

            return $builder->update([
                $builder->getModel()->getPendingDeleteColumn() => 0,
            ]);
        });
    }

    /**
     * Add the with-pending-deletes extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithPendingDeletes(Builder $builder): void
    {
        $builder->macro('withPendingDeletes', function (Builder $builder, $withPendingDeletes = true) {
            if (! $withPendingDeletes) {
                return $builder->withoutPendingDeletes();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-pending-deletes extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithoutPendingDeletes(Builder $builder): void
    {
        $builder->macro('withoutPendingDeletes', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->where(
                $model->getQualifiedPendingDeleteColumn(),
                '=',
                0
            );

            return $builder;
        });
    }

    /**
     * Add the only-pending-deletes extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addOnlyPendingDeletes(Builder $builder): void
    {
        $builder->macro('onlyPendingDeletes', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->where(
                $model->getQualifiedPendingDeleteColumn(),
                '!=',
                0
            );

            return $builder;
        });
    }
}
