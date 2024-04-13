@extends('sapphire::admin.master')

@section('title')
    <title>OATERS: System Overview</title>
@stop

@section('content')
    <div class="row card-deck mb-3">
        <counter class="col-md-3 p-0" title="{{trans('sapphire::admin.tenants.title')}}" value="{{$counters['tenants']}}" extra-title="{{trans('common::words.this_month')}}" extra-value="{{$counters['tenants_month']}}" color="green-2" white-text></counter>
        <counter class="col-md-3 p-0" title="{{trans('sapphire::admin.payments.title')}}" value="{{$counters['purchases']}}" extra-title="{{trans('common::words.this_month')}}" extra-value="{{$counters['purchases_month']}}" color="blue-2" white-text></counter>
        <counter class="col-md-3 p-0" title="{{trans('sapphire::admin.subscriptions.title')}}" value="{{$counters['subscriptions']}}" color="cyan-2" white-text></counter>
        <chart type="doughnut" id="subscriptions-chart" class="col-md-3 p-0" color="purple-3" style="max-height:300px" center-header title="{{trans('sapphire::admin.subscriptions.title')}}" url="{{url('sa/charts/subscriptions-pie')}}"></chart>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <chart id="subscriptions-line" color="cyan-10" ranged :default-range="defaultChartRange" title="{{trans('sapphire::admin.subscriptions.new')}}" url="{{url('sa/charts/subscriptions-line')}}"></chart>
        </div>
        <div class="col-md-6">
            <chart id="payments-line" color="pink-10" ranged :default-range="defaultChartRange" title="{{trans('sapphire::admin.payments.title')}}" url="{{url('sa/charts/payments-line')}}"></chart>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <card title="{{trans('sapphire::admin.tenants.new')}}" color="green-10">
                <vue-table id="latest-tenants" class="table-striped table-hover mb-0">
                    <column>{{trans('common::words.name')}}</column>
                    <column>{{trans('common::words.email')}}</column>
                    <column>{{trans('common::words.created_at')}}</column>
                    <template #rows>
                        @foreach($tenants as $tenant)
                            <tr>
                                <td>{{$tenant->name}}</td>
                                <td>{{$tenant->email}}</td>
                                <td>{{$tenant->created_at->setTimezone(config('app.timezone'))->format('d/m/Y h:i A')}}</td>
                            </tr>
                        @endforeach
                    </template>
                </vue-table>
            </card>
        </div>
        <div class="col-md-6">
            <card title="{{trans('sapphire::admin.payments.new')}}" color="blue-10">
                <vue-table id="latest-purchases" class="table-striped table-hover mb-0">
                    <column>{{trans('common::words.name')}}</column>
                    <column>{{trans('common::words.amount')}}</column>
                    <column>{{trans('common::words.created_at')}}</column>
                    <template #rows>
                        @foreach($purchases as $purchase)
                            <tr>
                                <td>{{$purchase->subscription->tenant->name}}</td>
                                <td>{{$purchase->subscription->tenant->email}}</td>
                                <td>{{$purchase->updated_at->setTimezone(config('app.timezone'))->format('d/m/Y h:i A')}}</td>
                            </tr>
                        @endforeach
                    </template>
                </vue-table>
            </card>
        </div>
    </div>
@stop

@push('scripts')
    @vite(['resources/js/sapphire/dashboard.js'])
@endpush
