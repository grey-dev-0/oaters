<?php

namespace App\Traits;

trait UsesTenantDatabase{
    /**
     * @inheritdoc
     */
    protected $connection = 'tenant';
}