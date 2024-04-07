<?php

namespace Modules\Sapphire\App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Modules\Sapphire\App\Models\Purchase;
use Modules\Sapphire\App\Models\Subscription;
use Modules\Sapphire\App\Models\Tenant;

class DashboardController extends Controller{
    public function getIndex(){
        $startOfMonth = now()->startOfMonth();
        return view('sapphire::admin.dashboard', [
            'tenants' => Tenant::orderBy('created_at', 'desc')->take(10)->get(['name', 'email', 'created_at']),
            'purchases' => Purchase::with('subscription.tenant:id,name,email')->orderBy('updated_at', 'desc')
                ->get(['id', 'subscription_id', 'updated_at']),
            'counters' => [
                'tenants' => Tenant::count(),
                'tenants_month' => Tenant::where('created_at', '>=', $startOfMonth)->count(),
                'purchases' => Purchase::count(),
                'purchases_month' => Purchase::where('updated_at', '>=', $startOfMonth)->count(),
                'subscriptions' => Subscription::count()
            ]
        ]);
    }

    public function postSubscriptionsPieChart(){
        return response()->json(\Chart::pie([
            Subscription::wherePaid(true)->where('expires_at', '>=', now())->count(),
            Subscription::wherePaid(true)->where('expires_at', '<', now())->count()
        ], [trans('common::words.active'), trans('common::words.expired')], ['#00c000', '#808000'])->get());
    }

    public function postSubscriptionsLineChart(){
        $range = explode(' to ', request('range'));
        $query = Subscription::wherePaid(true)->where('expires_at', '>', now())->whereBetween('subscriptions.updated_at',
            [new Carbon($range[0]), (new Carbon($range[1]))->addDay()])->groupBy('a_date');
        return response()->json(\Chart::line(
            $query->clone()->selectRaw('count(*) AS times, date(updated_at) AS a_date')->get()->pluck('times', 'a_date')->toArray(),
            trans('common::words.count'), '#00C0C0', 'rgba(0, 192, 192, 0.3)'
        )->line($query->selectRaw('sum(amount) AS amount, date(p.updated_at) AS a_date')->join('purchases AS p', 'subscriptions.id',
            'p.subscription_id')->whereExecuted(true)->groupBy('a_date')->get()->pluck('amount', 'a_date')->toArray(),
            trans('common::words.amount'), '#008000', 'rgba(0, 128, 0, 0.3)')->range()->get());
    }

    public function postPurchasesLineChart(){
        $range = explode(' to ', request('range'));
        $query = Purchase::whereExecuted(true)->whereBetween('updated_at', [new Carbon($range[0]),
            (new Carbon($range[1]))->addDay()])->groupBy('a_date');
        return response()->json(\Chart::line(
            $query->clone()->selectRaw('count(*) AS times, date(updated_at) AS a_date')->get()->pluck('times', 'a_date')->toArray(),
            trans('common::words.count'), '#ff00ff', 'rgba(255, 0, 255, 0.3)'
        )->line(
            $query->clone()->selectRaw('sum(amount) AS amount, date(updated_at) AS a_date')->get()->pluck('amount', 'a_date')->toArray(),
            trans('common::words.amount'), '#0000c0', 'rgba(0, 0, 192, 0.3)'
        )->range()->get());
    }
}