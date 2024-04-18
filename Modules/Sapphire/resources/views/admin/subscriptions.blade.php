@extends('sapphire::admin.master')

@section('title')
    <title>OATERS: Subscriptions</title>
@stop

@section('content')
    <div class="row">
        <breadcrumb>
            <bc-item url="{{url('sa')}}">Sapphire</bc-item>
            <bc-item active>{{trans('sapphire::admin.subscriptions.title')}}</bc-item>
        </breadcrumb>
    </div>

    <div class="row">
        <div class="col">
            <card title="{{trans('sapphire::admin.subscriptions.title')}}" color="cyan-2">
                <template #toolbar>
                    <div class="btn btn-sm btn-success" title="{{trans('sapphire::admin.subscriptions.create')}}" @click="addSubscription"><i class="fa fas fa-plus align-middle"></i></div>
                </template>
                <!--<vue-datafilter :cols="5">
                    <dt-filter name="name" type="text" label="{{trans('common::words.name')}}"></dt-filter>
                    <dt-filter name="email" type="text" label="{{trans('common::words.email')}}"></dt-filter>
                    <dt-filter name="subdomain" type="text" label="{{trans('common::words.subdomain')}}"></dt-filter>
                    <dt-filter name="tenants.created_at" type="date" label="{{trans('common::words.joined_at')}}" :options="{opens: 'left'}"></dt-filter>
                    <dt-filter name="expires_at" type="date" label="{{trans('common::words.expires_at')}}" :options="{opens: 'left'}"></dt-filter>
                </vue-datafilter>-->

                <vue-datatable datatable-id="subscriptions-table" ref="subscriptionsTable" :sort="[[5, 'desc']]">
                    <dt-column name="tenant.name" data="tenant.name">{{trans('sapphire::admin.subscriptions.tenant')}}</dt-column>
                    <dt-column name="modules.id" data="modules" :render="renderModules" :orderable="false">{{trans('sapphire::admin.subscriptions.modules')}}</dt-column>
                    <dt-column name="amount" :data="renderAmount">{{trans('common::words.amount')}}</dt-column>
                    <dt-column name="paid" data="paid" :render="renderPaid" :searchable="false">{{trans('common::words.paid')}}</dt-column>
                    <dt-column name="expires_at" data="expires_at" :searchable="false">{{trans('common::words.expires_at')}}</dt-column>
                    <dt-column name="created_at" data="created_at" :searchable="false">{{trans('common::words.created_at')}}</dt-column>
                    <dt-column :orderable="false" :searchable="false" class-name="nowrap" :data="null">
                        {{trans('common::words.actions')}}
                        <template #actions>
                            <div class="btn btn-sm btn-outline-primary edit" title="{{trans('common::words.edit')}}"><i class="fas fa-pen"></i></div>
                            <div class="btn btn-sm btn-outline-warning revoke" title="{{trans('sapphire::admin.subscriptions.revoke')}}"><i class="fas fa-ban"></i></div>
                            <div class="btn btn-sm btn-outline-danger delete" title="{{trans('common::words.delete')}}"><i class="fas fa-trash-alt"></i></div>
                        </template>
                    </dt-column>
                </vue-datatable>
            </card>
        </div>
    </div>

    <x-sapphire::admin.modals.subscription-form ref="createSubscription" id="add-subscription" color="green-2" title="{{trans('sapphire::admin.subscriptions.create')}}" :modules="$modules"/>
    <x-sapphire::admin.modals.subscription-form ref="updateSubscription" id="edit-subscription" color="blue-3" title="{{trans('sapphire::admin.subscriptions.update')}}" :modules="$modules" :edit="true"/>
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
    @vite(['resources/js/sapphire/subscriptions.js'])
@endpush