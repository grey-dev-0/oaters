<modal id="payments-modal" ref="paymentsModal" no-padding static size="xl" color="green-2">
    <template #header>
        <h3 class="text-white m-0">{{trans('sapphire::admin.payments.title')}} - @{{ openUser.name }}</h3>
        <span class="close text-muted" data-dismiss="modal">&times;</span>
    </template>
    <x-sapphire::admin.tables.payments :in-modal="true"/>
</modal>