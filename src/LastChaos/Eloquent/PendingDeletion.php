<?php

namespace LCFramework\Framework\LastChaos\Eloquent;

use LCFramework\Framework\LastChaos\Eloquent\Scopes\PendingDeletionScope;

trait PendingDeletion
{
    /**
     * Indicates if the model is currently force deleting.
     *
     * @var bool
     */
    protected $forceDeleting = false;

    /**
     * Boot the pending deletion trait for a model.
     *
     * @return void
     */
    public static function bootPendingDeletion()
    {
        static::addGlobalScope(new PendingDeletionScope());
    }

    /**
     * Initialize the pending deletion trait for an instance.
     *
     * @return void
     */
    public function initializePendingDeletion()
    {
        if (!isset($this->casts[$this->getPendingDeleteColumn()])) {
            $this->casts[$this->getPendingDeleteColumn()] = 'integer';
        }
    }

    /**
     * Force a hard delete on a pending deleted model.
     *
     * @return bool|null
     */
    public function forceDelete()
    {
        $this->forceDeleting = true;

        return tap($this->delete(), function ($deleted) {
            $this->forceDeleting = false;

            if ($deleted) {
                $this->fireModelEvent('forceDeleted', false);
            }
        });
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return mixed
     */
    protected function performDeleteOnModel()
    {
        if ($this->forceDeleting) {
            return tap($this->setKeysForSaveQuery($this->newModelQuery())->forceDelete(), function () {
                $this->exists = false;
            });
        }

        return $this->runPendingDelete();
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function runPendingDelete(): void
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp()->addDays()->unix();

        $columns = [$this->getPendingDeleteColumn() => $time];

        $this->{$this->getPendingDeleteColumn()} = $time;

        if ($this->timestamps && !is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));

        $this->fireModelEvent('pendingDeletion', false);
    }

    /**
     * Restore a pending-deletion model instance.
     *
     * @return bool|null
     */
    public function restore(): ?bool
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getPendingDeleteColumn()} = null;

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Restore a pending-deletion model instance without raising any events.
     *
     * @return bool|null
     */
    public function restoreQuietly()
    {
        return static::withoutEvents(fn() => $this->restore());
    }

    /**
     * Determine if the model instance is pending-deletion.
     *
     * @return bool
     */
    public function pendingDeletion()
    {
        return !is_null($this->{$this->getPendingDeleteColumn()});
    }

    /**
     * Register a "pendingDeletion" model event callback with the dispatcher.
     *
     * @param string|\Closure $callback
     *
     * @return void
     */
    public static function pendingDeleted(string|\Closure $callback): void
    {
        static::registerModelEvent('pendingDeletion', $callback);
    }

    /**
     * Register a "restoring" model event callback with the dispatcher.
     *
     * @param string|\Closure $callback
     *
     * @return void
     */
    public static function restoring(string|\Closure $callback): void
    {
        static::registerModelEvent('restoring', $callback);
    }

    /**
     * Register a "restored" model event callback with the dispatcher.
     *
     * @param string|\Closure $callback
     *
     * @return void
     */
    public static function restored(string|\Closure $callback): void
    {
        static::registerModelEvent('restored', $callback);
    }

    /**
     * Register a "forceDeleted" model event callback with the dispatcher.
     *
     * @param string|\Closure $callback
     *
     * @return void
     */
    public static function forceDeleted(string|\Closure $callback): void
    {
        static::registerModelEvent('forceDeleted', $callback);
    }

    /**
     * Determine if the model is currently force deleting.
     *
     * @return bool
     */
    public function isForceDeleting(): bool
    {
        return $this->forceDeleting;
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getPendingDeleteColumn(): string
    {
        return defined(static::class . '::PENDING_DELETE') ? static::PENDING_DELETE : 'a_delete_delay';
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedPendingDeleteColumn(): string
    {
        return $this->qualifyColumn($this->getPendingDeleteColumn());
    }
}
