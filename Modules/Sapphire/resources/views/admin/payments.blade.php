@extends('sapphire::admin.master')

@section('title')
    <title>OATERS: Payments</title>
@stop

@section('content')
    <div class="row">
        <breadcrumb>
            <bc-item url="{{url('sa')}}">Sapphire</bc-item>
            <bc-item active>{{trans('sapphire::admin.payments.title')}}</bc-item>
        </breadcrumb>
    </div>

    <div class="row">
        <div class="col">
            <card title="{{trans('sapphire::admin.payments.title')}}" color="blue-2">
                <vue-datafilter :cols="5">
                    <dt-filter name="t.name" type="text" label="{{trans('sapphire::admin.payments.tenant')}}"></dt-filter>
                    <dt-filter name="amount" type="text" label="{{trans('common::words.amount')}}"></dt-filter>
                    <dt-filter name="executed" type="select" label="{{trans('common::words.executed')}}" :values="{'1': locale.common.yes, '-1': locale.common.no}"></dt-filter>
                    <dt-filter name="purchases.created_at" type="date" label="{{trans('common::words.created_at')}}" :options="{opens: 'left'}"></dt-filter>
                    <dt-filter name="purchases.updated_at" type="date" label="{{trans('common::words.paid_at')}}" :options="{opens: 'left'}"></dt-filter>
                </vue-datafilter>

                <x-sapphire::admin.tables.payments/>
            </card>
        </div>
    </div>
@stop

@push('scripts')
    <script type="text/javascript">
        locale.common = @json([
            'yes' => trans('common::words.yes'),
            'no' => trans('common::words.no'),
            'unpaid' => trans('common::words.unpaid')
        ]);
    </script>
    @vite(['resources/js/sapphire/payments.js'])
@endpush