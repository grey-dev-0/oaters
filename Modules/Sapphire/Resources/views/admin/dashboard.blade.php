@extends('sapphire::admin.master')

@section('title')
    <title>OATERS: System Overview</title>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card-deck mb-3">
                <counter title="{{trans('sapphire::admin.tenants.title')}}" value="123" extra-title="xyz" extra-value="456" color="green-2" white-text></counter>
                <counter title="{{trans('sapphire::admin.payments.title')}}" value="321" extra-title="abc" extra-value="456" color="blue-2" white-text></counter>
                <counter title="{{trans('sapphire::admin.subscriptions.title')}}" value="654" color="cyan-2" white-text></counter>
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script type="text/javascript" src="{{asset('resources/js/bundles/sapphire/admin_dashboard.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/views/sapphire/admin_dashboard.min.js')}}"></script>
@endpush
