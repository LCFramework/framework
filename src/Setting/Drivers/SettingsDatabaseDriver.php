<?php

namespace LCFramework\Framework\Setting\Drivers;

use LCFramework\Framework\Setting\Models\Setting;

class SettingsDatabaseDriver extends SettingsDriverBase
{
    public function save(): void
    {
        ['key' => $keyColumn, 'value' => $valueColumn] = $this->getColumns();

        if (! empty($this->updated)) {
            Setting::query()
                ->upsert(
                    collect($this->updated)
                        ->map(fn (string $key): array => [
                            $keyColumn => $key,
                            $valueColumn => $this->encode($this->get($key)),
                        ])
                        ->all(),
                    $keyColumn
                );

            foreach ($this->updated as $key) {
                $this->setCache($key, $this->get($key));
            }
        }

        if (! empty($this->deleted)) {
            Setting::query()
                ->whereIn($keyColumn, $this->deleted)
                ->delete();

            foreach ($this->deleted as $key) {
                $this->clearCache($key);
            }
        }
    }

    protected function load(string $key)
    {
        ['key' => $keyColumn] = $this->getColumns();

        $setting = Setting::query()
            ->where($keyColumn, '=', $key)
            ->first('value');

        if ($setting !== null) {
            return $this->decode($setting->value);
        }

        return null;
    }

    protected function getColumns(): array
    {
        return config('lcframework.settings.database.columns');
    }
}
