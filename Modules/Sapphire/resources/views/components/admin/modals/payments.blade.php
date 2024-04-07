<modal id="payments-modal" ref="paymentsModal" no-padding static size="xl" color="green-2">
    <template #header>{{trans('sapphire::admin.payments.title')}} - @{{ openUser.name }}</template>
    <x-sapphire::admin.tables.payments :in-modal="true"/>
</modal>