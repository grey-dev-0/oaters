@props(['inModal' => false])

<vue-datatable datatable-id="payments-table" ref="paymentsTable" url="{{url('sa/payments')}}" :sort="[[{{$inModal? 3 : 4}}, 'desc']]" @if($inModal) :ajax-data="{tenant_id: openUser.id}" deferred @endif>
    @if(!$inModal)
        <dt-column name="t.name" data="name">{{trans('sapphire::admin.payments.tenant')}}</dt-column>
    @endif
    <dt-column name="amount" data="amount" :render="renderAmount" :searchable="false">{{trans('common::words.amount')}}</dt-column>
    <dt-column name="executed" data="executed" :render="renderExecuted" :searchable="false">{{trans('common::words.executed')}}</dt-column>
    <dt-column name="purchases.created_at" data="created_at" :searchable="false">{{trans('common::words.created_at')}}</dt-column>
    <dt-column name="purchases.updated_at" :data="renderPaidAt" :searchable="false">{{trans('common::words.paid_at')}}</dt-column>
</vue-datatable>