@extends('sapphire::admin.master')

@section('title')
    <title>OATERS: Tenants</title>
@stop

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
                <vue-datafilter :cols="5" datatable-ref="tenantsTable">
                    <dt-filter name="name" type="text" label="{{trans('common::words.name')}}"></dt-filter>
                    <dt-filter name="email" type="text" label="{{trans('common::words.email')}}"></dt-filter>
                    <dt-filter name="d.domain" type="text" label="{{trans('common::words.subdomain')}}"></dt-filter>
                    <dt-filter name="tenants.created_at" type="date" label="{{trans('common::words.joined_at')}}" :options="{opens: 'left'}"></dt-filter>
                    <dt-filter name="expires_at" type="date" label="{{trans('common::words.expires_at')}}" :options="{opens: 'left'}"></dt-filter>
                </vue-datafilter>

                <vue-datatable datatable-id="tenants-table" ref="tenantsTable" :ajax-complete="onDatatableDraw">
                    <dt-column name="name" data="name">{{trans('common::words.name')}}</dt-column>
                    <dt-column name="email" data="email">{{trans('common::words.email')}}</dt-column>
                    <dt-column name="d.domain" data="domain">{{trans('common::words.subdomain')}}</dt-column>
                    <dt-column name="tenants.created_at" data="created_at" :searchable="false">{{trans('common::words.joined_at')}}</dt-column>
                    <dt-column name="expires_at" data="expires_at" :searchable="false">{{trans('common::words.expires_at')}}</dt-column>
                    <dt-column :orderable="false" :searchable="false" class-name="nowrap" name="actions" :data="null">
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
    @vite(['resources/js/sapphire/tenants.js'])
@endpush