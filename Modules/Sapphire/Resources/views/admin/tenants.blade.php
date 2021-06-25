@extends('sapphire::admin.master')

@section('title')
    <title>OATERS: Tenants</title>
@stop

@push('styles')
    <link rel="stylesheet" href="{{asset('resources/css/jquery.dataTables.min.css')}}" type="text/css">
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
                <vue-datatable datatable-id="tenants-table">
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
        export default {
            components: {BcItem, Breadcrumb, Card, VueDatatable, DtColumn}
        }
    </script>
    <script type="text/javascript" src="{{asset('resources/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/jquery.dataTables.bs4.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/lodash.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/bundles/sapphire/tenants.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/views/sapphire/tenants.min.js')}}"></script>
@endpush