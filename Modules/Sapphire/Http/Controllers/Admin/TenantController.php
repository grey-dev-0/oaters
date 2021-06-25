<?php

namespace Modules\Sapphire\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Modules\Sapphire\Entities\Tenant;

class TenantController extends Controller{
    public function postIndex(){
        $response = \DataTables::of(Tenant::selectRaw('tenants.*, s.expires_at')->leftJoin('subscriptions AS s', 's.tenant_id', 'tenants.id')
            ->orderBy('s.id', 'desc')->groupBy('s.id'))->filter(function($query){
                \DataTablesHelper::filterByDate($query, ['tenants.created_at', 'expires_at']);
            });

        foreach(['created_at', 'expires_at'] as $attribute)
            $response->editColumn($attribute, function($tenant) use($attribute){
                if(empty($tenant->$attribute))
                    return null;
                else
                    return (new Carbon($tenant->$attribute))->format('d/m/Y h:i:s A');
            });

        return $response->make(true);
    }
}