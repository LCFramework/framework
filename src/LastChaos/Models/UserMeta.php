<?php

namespace LCFramework\Framework\LastChaos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LCFramework\Framework\Auth\Models\User;
use LCFramework\Framework\Transformer\Facade\Transformer;

class UserMeta extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'a_index';

    public function getTable(): string
    {
        return config('lcframework.last_chaos.database.auth').'.t_users';
    }

    public function getFillable(): array
    {
        return Transformer::transform(
            'auth.user_meta.fillable',
            [
                'a_enable',
                'a_zone_num',
            ]
        );
    }

    public function getCasts(): array
    {
        return Transformer::transform(
            'auth.user_meta.casts',
            [
                ...parent::getCasts(),
                'a_enable' => 'integer',
                'a_zone_num' => 'integer',
            ]
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'a_idname',
            'user_id'
        );
    }
}
