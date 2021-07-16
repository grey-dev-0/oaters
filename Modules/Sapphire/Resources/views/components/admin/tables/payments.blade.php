@props(['inModal' => false])

<vue-datatable datatable-id="payments-table" ref="paymentsTable" url="{{url('sa/payments')}}" :sort="[[{{$inModal? 3 : 4}}, 'desc']]" @if($inModal) :ajax-data="{tenant_id: openUser.id}" deferred @endif>
    @if(!$inModal)
        <dt-column name="name" data="name">{{trans('sapphire::admin.payments.tenant')}}</dt-column>
    @endif
    <dt-column name="amount" data="amount">{{trans('common::words.amount')}}</dt-column>
    <dt-column name="executed" data="executed">{{trans('common::words.executed')}}</dt-column>
    <dt-column name="created_at" data="created_at">{{trans('common::words.created_at')}}</dt-column>
    <dt-column name="updated_at" data="updated_at" last>{{trans('common::words.paid_at')}}</dt-column>
</vue-datatable>