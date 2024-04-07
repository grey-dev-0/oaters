<?php

namespace Modules\Sapphire\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Sapphire\App\Models\Module;
use Modules\Sapphire\App\Models\Subscription;
use Modules\Sapphire\App\Models\Tenant;

class TenantController extends Controller{
    public function postIndex(){
        $response = \DataTables::of(Tenant::selectRaw('tenants.*, s.id AS subscription_id, s.expires_at')
            ->leftJoin('subscriptions AS s', 's.tenant_id', 'tenants.id')->orderBy('s.id', 'desc')->groupBy('s.id'))
            ->filter(function($query){
                \DataTablesHelper::filterByDate($query, ['tenants.created_at', 'expires_at']);
            });
        \DataTablesHelper::formatTimestampColumns($response, ['created_at', 'expires_at']);
        return $response->make(true);
    }

    public function getModules(Subscription $subscription){
        return response()->json([
            'modules' => $subscription->modules()->pluck('name')
        ]);
    }

    public function getSubscriptions(){
        return view('sapphire::admin.subscriptions', ['modules' => Module::all()]);
    }

    public function postSubscriptions(){
        $response = \DataTables::of(Subscription::with(['tenant:id,name', 'modules:id,name']));
        \DataTablesHelper::formatTimestampColumns($response, ['created_at', 'expires_at']);
        return $response->make(true);
    }

    public function postAutocomplete(){
        return response()->json(['suggestions' => (!empty($query = request('query')))?
            Tenant::where('email', 'like', "%$query%")->orWhere('name', 'like', "%$query%")
                ->selectRaw('id, CONCAT(name, \' - \', email) AS name')->take(request('limit'))->get()->pluck('name', 'id')
            : []]);
    }
}