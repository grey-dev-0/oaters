@extends('sapphire::admin.master')

@section('title')
    <title>OATERS: System Overview</title>
@stop

@section('content')
    <div class="row card-deck">
        <counter class="col-md-3 mb-3 p-0" title="{{trans('sapphire::admin.tenants.title')}}" value="123" extra-title="xyz" extra-value="456" color="green-2" white-text></counter>
        <counter class="col-md-3 mb-3 p-0" title="{{trans('sapphire::admin.payments.title')}}" value="321" extra-title="abc" extra-value="456" color="blue-2" white-text></counter>
        <counter class="col-md-3 mb-3 p-0" title="{{trans('sapphire::admin.subscriptions.title')}}" value="654" color="cyan-2" white-text></counter>
    </div>
@stop

@push('scripts')
    <script type="text/javascript" src="{{asset('resources/js/bundles/sapphire/admin_dashboard.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/views/sapphire/admin_dashboard.min.js')}}"></script>
@endpush
