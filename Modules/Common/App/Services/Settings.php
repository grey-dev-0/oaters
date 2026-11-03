<?php

namespace Modules\Common\App\Services;

use Modules\Common\App\Models\Setting;

class Settings
{
    /**
     * Resolves the stored tenant value for the given key, falling back to the
     * consuming module's declared default when absent.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = Setting::query()->where('key', $key)->value('value');

        return $value === null ? $default : $value;
    }

    /**
     * Stores (or overwrites) the tenant value for the given key.
     */
    public function set(string $key, mixed $value): void
    {
        Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
