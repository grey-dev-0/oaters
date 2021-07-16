@extends('sapphire::admin.master')

@section('title')
    <title>OATERS: Payments</title>
@stop

@push('styles')
    <link rel="stylesheet" href="{{asset('resources/css/jquery.dataTables.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('resources/css/daterangepicker.min.css')}}" type="text/css">
@endpush

@section('content')
    <div class="row">
        <breadcrumb>
            <bc-item url="{{url('sa')}}">Sapphire</bc-item>
            <bc-item active>{{trans('sapphire::admin.payments.title')}}</bc-item>
        </breadcrumb>
    </div>

    <div class="row">
        <div class="col">
            <card title="{{trans('sapphire::admin.payments.title')}}" color="green-2">
                <vue-datafilter :cols="5">
                    <dt-filter name="name" type="text" label="{{trans('sapphire::admin.payments.tenant')}}"></dt-filter>
                    <dt-filter name="purchases.created_at" type="date" label="{{trans('common::words.joined_at')}}" :options="{opens: 'left'}"></dt-filter>
                    <dt-filter name="purchases.updated_at" type="date" label="{{trans('common::words.expires_at')}}" :options="{opens: 'left'}"></dt-filter>
                </vue-datafilter>

                <x-sapphire::admin.tables.payments/>
            </card>
        </div>
    </div>
@stop

@push('scripts')
    <script type="text/javascript" src="{{asset('resources/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/jquery.dataTables.bs4.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/lodash.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/daterangepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/bundles/sapphire/tenants.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/views/sapphire/payments.min.js')}}"></script>
@endpush