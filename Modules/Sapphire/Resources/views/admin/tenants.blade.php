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
                <vue-datafilter :cols="5">
                    <dt-filter name="name" type="text" label="{{trans('common::words.name')}}"></dt-filter>
                    <dt-filter name="email" type="text" label="{{trans('common::words.email')}}"></dt-filter>
                    <dt-filter name="subdomain" type="text" label="{{trans('common::words.subdomain')}}"></dt-filter>
                    <dt-filter name="tenants.created_at" type="date" label="{{trans('common::words.joined_at')}}" :options="{opens: 'left'}"></dt-filter>
                    <dt-filter name="expires_at" type="date" label="{{trans('common::words.expires_at')}}" :options="{opens: 'left'}"></dt-filter>
                </vue-datafilter>

                <vue-datatable datatable-id="tenants-table" ref="tenantsTable" :ajax-complete="onDatatableDraw">
                    <dt-column name="name" data="name">{{trans('common::words.name')}}</dt-column>
                    <dt-column name="email" data="email">{{trans('common::words.email')}}</dt-column>
                    <dt-column name="subdomain" data="subdomain">{{trans('common::words.subdomain')}}</dt-column>
                    <dt-column name="tenants.created_at" data="created_at" :searchable="false">{{trans('common::words.joined_at')}}</dt-column>
                    <dt-column name="expires_at" data="expires_at" :searchable="false">{{trans('common::words.expires_at')}}</dt-column>
                    <dt-column :orderable="false" :searchable="false" class-name="nowrap" :data="null">
                        {{trans('common::words.actions')}}
                        <template #actions>
                            <div class="btn btn-sm btn-outline-primary payments" title="{{trans('sapphire::admin.payments.title')}}"><i class="fas fa-dollar-sign"></i></div>
                            <div class="btn btn-sm btn-outline-info modules" title="{{trans('sapphire::admin.common.modules')}}" data-id="subscription_id"><i class="fas fa-layer-group"></i></div>
                            <div class="btn btn-sm btn-outline-success extend" title="{{trans('sapphire::admin.common.extend')}}"><i class="fas fa-calendar-plus"></i></div>
                            <div class="btn btn-sm btn-outline-warning revoke" title="{{trans('sapphire::admin.tenants.revoke')}}"><i class="fas fa-ban"></i></div>
                        </template>
                    </dt-column>
                </vue-datatable>
            </card>
        </div>
    </div>

    <x-sapphire::admin.modals.payments/>
@stop

@push('scripts')
    <script type="text/javascript">
        @php
        $locale = \Arr::only(trans('common::words'), ['yes', 'no', 'na', 'unpaid']) + [
            'modules' => trans('sapphire::admin.common.modules')
        ];
        @endphp
        locale.common = @json($locale);
    </script>
    <script type="text/javascript" src="{{asset('resources/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/jquery.dataTables.bs4.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/lodash.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/daterangepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/bundles/common/datatable_with_modal.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/views/sapphire/tenants.min.js')}}"></script>
@endpush