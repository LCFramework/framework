<?php

namespace LCFramework\Framework\LastChaos\Support;

use LCFramework\Framework\Transformer\Facade\Transformer;

class CharacterJob
{
    protected static ?array $jobs = null;

    public static function getJobs(): array
    {
        if (! static::$jobs !== null) {
            return static::$jobs;
        }

        return static::$jobs = Transformer::transform(
            'character.jobs',
            [
                ['Titan', 'Warmaster', 'Titan'],
                ['Knight', 'Royal Knight', 'Templar'],
                ['Healer', 'Archer', 'Cleric'],
                ['Mage', 'Wizard', 'Witch'],
                ['Rogue', 'Ranger', 'Assassin'],
                ['Sorcerer', 'Specialist', 'Elementalist'],
                ['Nightshadow'],
                ['Ex-Rogue', 'Ex-Ranger', 'Ex-Assassin'],
                ['ArchMage', 'ArchWizard', 'ArchWitch'],
            ]
        );
    }

    public static function title(int $class, int $job): ?string
    {
        $jobs = static::get($class);

        return $jobs[$job] ?? null;
    }

    public static function index(int $class, string $job): ?int
    {
        $jobs = static::get($class);

        if ($key = array_search($job, $jobs)) {
            return $key;
        }

        return null;
    }

    public static function get(int $class): array
    {
        return static::$jobs[$class] ?? [];
    }
}
