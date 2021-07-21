<?php

namespace Modules\Sapphire\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Sapphire\Entities\Subscription;

class DashboardController extends Controller{
    public function postSubscriptionsPieChart(){
        return response()->json([
            'datasets' => [
                [
                    'data' => [
                        Subscription::wherePaid(true)->where('expires_at', '>=', now())->count(),
                        Subscription::wherePaid(true)->where('expires_at', '<', now())->count()
                    ],
                    'backgroundColor' => [
                        '#00c000',
                        '#808000'
                    ]
                ]
            ],
            'labels' => [
                trans('common::words.active'),
                trans('common::words.expired')
            ]
        ]);
    }
}