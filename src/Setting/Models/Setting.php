<?php

namespace LCFramework\Framework\Setting\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public function getTable()
    {
        return config('lcframework.settings.database.table');
    }

    public function getFillable()
    {
        return array_values(
            config('lcframework.settings.database.columns')
        );
    }
}
