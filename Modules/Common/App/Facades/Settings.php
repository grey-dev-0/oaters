<?php

namespace Modules\Common\App\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Common\App\Services\Settings as SettingsService;

class Settings extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return SettingsService::class;
    }
}
