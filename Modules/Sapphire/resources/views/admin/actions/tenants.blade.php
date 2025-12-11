<div vue-template>
    <div class="btn btn-sm btn-outline-primary payments mr-2" title="{{trans('sapphire::admin.payments.title')}}" @click="openPayments"><i class="fas fa-dollar-sign"></i></div>
    <div @class(['btn', 'btn-sm', 'btn-outline-info', 'modules', 'mr-2', 'disabled' => !$tenant->subscription_id]) title="{{trans('sapphire::admin.common.modules')}}" data-id="{{$tenant->subscription_id}}" @click="toggleModules"><i class="fas fa-layer-group"></i></div>
    <div class="btn btn-sm btn-outline-success extend mr-2" title="{{trans('sapphire::admin.common.extend')}}" @click="extendTenant"><i class="fas fa-calendar-plus"></i></div>
    <div class="btn btn-sm btn-outline-warning revoke" title="{{trans('sapphire::admin.tenants.revoke')}}" @click="revokeTenant"><i class="fas fa-ban"></i></div>
</div>
