@extends('sapphire::admin.master')

@section('title')
    <title>OATERS: System Overview</title>
@stop

@push('styles')
    <link rel="stylesheet" href="{{asset('resources/css/daterangepicker.min.css')}}" type="text/css">
@endpush

@section('content')
    <div class="row card-deck mb-3">
        <counter class="col-md-3 p-0" title="{{trans('sapphire::admin.tenants.title')}}" value="123" extra-title="xyz" extra-value="456" color="green-2" white-text></counter>
        <counter class="col-md-3 p-0" title="{{trans('sapphire::admin.payments.title')}}" value="321" extra-title="abc" extra-value="456" color="blue-2" white-text></counter>
        <counter class="col-md-3 p-0" title="{{trans('sapphire::admin.subscriptions.title')}}" value="654" color="cyan-2" white-text></counter>
        <chart type="doughnut" id="subscriptions-chart" class="col-md-3 p-0" color="purple-3" center-header title="{{trans('sapphire::admin.subscriptions.title')}}" url="{{url('sa/charts/subscriptions-pie')}}"></chart>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <chart id="subscriptions-line" color="cyan-10" ranged title="{{trans('sapphire::admin.subscriptions.title')}}" url="{{url('sa/charts/subscriptions-line')}}"></chart>
        </div>
        <div class="col-md-6">
            <chart id="payments-line" color="pink-10" ranged title="{{trans('sapphire::admin.payments.title')}}" url="{{url('sa/charts/payments-line')}}"></chart>
        </div>
    </div>
@stop

@push('scripts')
    <script type="text/javascript" src="{{asset('resources/js/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/daterangepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/chart.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/bundles/sapphire/admin_dashboard.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/views/sapphire/admin_dashboard.min.js')}}"></script>
@endpush
