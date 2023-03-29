<?php

namespace Modules\Sapphire\Traits;

trait Authorizable{
    /**
     * Gets the privileges that the model is authorized to execute.
     *
     * @return string[]
     * @throws \Exception
     */
    private function privileges(){
        $cacheKey = config('database.connections.tenant.username').".role-{$this->role_id}";
        return cache()->rememberForever($cacheKey, fn() => $this->role()->first()->privileges->pluck('name')->toArray());
    }

    /**
     * Determines whether the model has access to the provided privilege.
     *
     * @param string $privilege The privilege to search for.
     * @return bool
     * @throws \Exception
     */
    public function accesses($privilege){
        return in_array($privilege, $this->privileges());
    }
}