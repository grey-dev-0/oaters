<?php

namespace Modules\Sapphire\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Sapphire\App\Models\Purchase;

class PurchaseController extends Controller{
    public function postIndex(){
        $response = \DataTables::of(Purchase::selectRaw('amount, executed, purchases.created_at, purchases.updated_at, name')->join('subscriptions AS s', 'subscription_id', 's.id')->join('tenants AS t', 'tenant_id', 't.id'))->filter(function($query){
            \DataTablesHelper::filterByDate($query, ['purchases.created_at', 'purchases.updated_at']);
            if(!empty($tenantId = request('tenant_id')))
                $query->where('t.id', $tenantId);
            $columns = \DataTablesHelper::getColumns();
            if(!empty($amount = request("columns.{$columns['amount']}.search.value")))
                $query->where('amount', $amount);
            if(!empty($executed = request("columns.{$columns['executed']}.search.value")))
                $query->where('executed', $executed == '1');
        });
        \DataTablesHelper::formatTimestampColumns($response, ['created_at', 'updated_at']);

        return $response->make(true);
    }
}
