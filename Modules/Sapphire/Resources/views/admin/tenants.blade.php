@extends('sapphire::admin.master')

@section('title')
    <title>OATERS: Tenants</title>
@stop

@push('styles')
    <link rel="stylesheet" href="{{asset('resources/css/jquery.dataTables.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('resources/css/daterangepicker.min.css')}}" type="text/css">
@endpush

@section('content')
    <div class="row">
        <breadcrumb>
            <bc-item url="{{url('sa')}}">Sapphire</bc-item>
            <bc-item active>{{trans('sapphire::admin.tenants.title')}}</bc-item>
        </breadcrumb>
    </div>

    <div class="row">
        <div class="col">
            <card title="{{trans('sapphire::admin.tenants.title')}}" color="green-2">
                <vue-datafilter cols="5">
                    <dt-filter name="name" type="text" label="{{trans('common::words.name')}}"></dt-filter>
                    <dt-filter name="email" type="text" label="{{trans('common::words.email')}}"></dt-filter>
                    <dt-filter name="subdomain" type="text" label="{{trans('common::words.subdomain')}}"></dt-filter>
                    <dt-filter name="tenants.created_at" type="date" label="{{trans('common::words.joined_at')}}" :options="{opens: 'left'}"></dt-filter>
                    <dt-filter name="expires_at" type="date" label="{{trans('common::words.expires_at')}}" :options="{opens: 'left'}"></dt-filter>
                </vue-datafilter>

                <vue-datatable datatable-id="tenants-table" ref="tenantsTable">
                    <dt-column name="name" data="name">{{trans('common::words.name')}}</dt-column>
                    <dt-column name="email" data="email">{{trans('common::words.email')}}</dt-column>
                    <dt-column name="subdomain" data="subdomain">{{trans('common::words.subdomain')}}</dt-column>
                    <dt-column name="tenants.created_at" data="created_at" :searchable="false">{{trans('common::words.joined_at')}}</dt-column>
                    <dt-column name="expires_at" data="expires_at" :searchable="false">{{trans('common::words.expires_at')}}</dt-column>
                    <dt-column :orderable="false" :searchable="false" :data="renderActions" last>{{trans('common::words.actions')}}</dt-column>
                </vue-datatable>
            </card>
        </div>
    </div>
@stop

@push('scripts')
    <script>
        import {BcItem, Breadcrumb, Card, VueDatatable} from "resources/bundles/sapphire/tenants";
        import DtColumn from "resources/components/datatable/column";
        import VueDatafilter from "../../../../../resources/components/datatable/datafilter";
        import DtFilter from "../../../../../resources/components/datatable/filter-input";
        export default {
            components: {DtFilter, VueDatafilter, BcItem, Breadcrumb, Card, VueDatatable, DtColumn}
        }
    </script>
    <script type="text/javascript" src="{{asset('resources/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/jquery.dataTables.bs4.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/lodash.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/daterangepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/bundles/sapphire/tenants.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/views/sapphire/tenants.min.js')}}"></script>
@endpush