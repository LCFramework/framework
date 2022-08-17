<?php

namespace LCFramework\Framework\LastChaos\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LCFramework\Framework\Auth\Models\User;
use LCFramework\Framework\Transformer\Facade\Transformer;

class Character extends Model
{
    public function getTable(): string
    {
        return config('lcframework.last_chaos.database.db') . '.t_characters';
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
                'a_level'
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

    public function job_title(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->determineJobTitle()
        );
    }

    protected function determineJobTitle(): ?string
    {
        return match ($this->a_job) {
            0 => $this->determineTitanJob(),
            1 => $this->determineKnightJob(),
            2 => $this->determineHealerJob(),
            3 => $this->determineMageJob(),
            4 => $this->determineRogueJob(),
            5 => $this->determineSorcererJob(),
            6 => 'Nightshadow',
            7 => $this->determineExRogueJob(),
            8 => $this->determineArchMageJob(),
            default => null,
        };
    }

    protected function determineTitanJob(): string
    {
        return match ($this->a_job2) {
            1 => 'Warmaster',
            2 => 'Highlander',
            default => 'Titan',
        };
    }

    protected function determineKnightJob(): string
    {
        return match ($this->a_job2) {
            1 => 'Royal Knight',
            2 => 'Templar',
            default => 'Knight',
        };
    }

    protected function determineHealerJob(): string
    {
        return match ($this->a_job2) {
            1 => 'Archer',
            2 => 'Cleric',
            default => 'Healer',
        };
    }

    protected function determineMageJob(): string
    {
        return match ($this->a_job2) {
            1 => 'Wizard',
            2 => 'Witch',
            default => 'Mage',
        };
    }

    protected function determineRogueJob(): string
    {
        return match ($this->a_job2) {
            1 => 'Ranger',
            2 => 'Assassin',
            default => 'Rogue',
        };
    }

    protected function determineSorcererJob(): string
    {
        return match ($this->a_job2) {
            1 => 'Specialist',
            2 => 'Elementalist',
            default => 'Sorcerer',
        };
    }

    protected function determineExRogueJob(): string
    {
        return match ($this->a_job2) {
            1 => 'Ex-Ranger',
            2 => 'Ex-Assassin',
            default => 'Ex-Rogue',
        };
    }

    protected function determineArchMageJob(): string
    {
        return match ($this->a_job2) {
            1 => 'ArchWizard',
            2 => 'ArchWitch',
            default => 'ArchMage',
        };
    }
}
