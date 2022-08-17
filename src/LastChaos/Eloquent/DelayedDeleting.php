<?php

namespace LCFramework\Framework\LastChaos\Eloquent;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait DelayedDeleting
{
    public bool $forceDeleting = false;

    public function trashed(): bool
    {
        return $this->{$this->getDelayedDeletingColumn()} !== 0;
    }

    public function delete()
    {
        if (! $this->exists) {
            return;
        }

        if (! $this->forceDeleting) {
            $this->forceFill([
                $this->getDelayedDeletingColumn() => now()->addDay()->unix(),
            ])->save();

            return;
        }

        parent::delete();
    }

    public function forceDelete()
    {
        $this->forceDeleting = true;

        $this->delete();
    }

    public function restore()
    {
        if (! $this->exists) {
            return;
        }

        $this->forceFill([
            $this->getDelayedDeletingColumn() => 0,
        ])->save();
    }

    public function withTrashed(Builder $query): Builder
    {
        return $query->where(
            $this->getDelayedDeletingColumn(),
            '>=',
            0
        );
    }

    public function withoutTrashed(Builder $query): Builder
    {
        return $query->where(
            $this->getDelayedDeletingColumn(),
            '=',
            0
        );
    }

    public function onlyTrashed(Builder $query): Builder
    {
        return $query->where(
            $this->getDelayedDeletingColumn(),
            '!=',
            0
        );
    }

    protected function getDelayedDeletingColumn()
    {
        return 'a_deletedelay';
    }
}
