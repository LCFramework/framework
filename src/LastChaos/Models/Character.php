<?php

namespace LCFramework\Framework\LastChaos\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use LCFramework\Framework\Auth\Models\User;
use LCFramework\Framework\LastChaos\Eloquent\Scopes\PendingDeletionScope;
use LCFramework\Framework\LastChaos\Support\CharacterJob;
use LCFramework\Framework\Transformer\Facade\Transformer;

class Character extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $primaryKey = 'a_index';

    public function getTable(): string
    {
        return config('lcframework.last_chaos.database.db').'.t_characters';
    }

    public function getFillable(): array
    {
        return Transformer::transform(
            'auth.character.fillable',
            [
                'a_user_index',
                'a_name',
                'a_nick',
                'a_job',
                'a_job2',
                'a_level',
                'a_admin',
                'a_deletedelay',
            ]
        );
    }

    public function getCasts(): array
    {
        return Transformer::transform(
            'auth.character.casts',
            [
                ...parent::getCasts(),
                'a_createdate' => 'datetime',
                'a_admin' => 'integer',
            ]
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'a_user_index',
            'user_code'
        );
    }

    public function jobTitle(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => CharacterJob::title($this->a_job, $this->a_job2)
        );
    }

    public function isAdmin(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->a_admin === 10
        );
    }

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new PendingDeletionScope());
    }

    public function getDeletedAtColumn()
    {
        return 'a_deletedelay';
    }
}
