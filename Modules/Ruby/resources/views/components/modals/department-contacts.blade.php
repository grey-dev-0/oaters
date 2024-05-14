<modal id="contacts-modal" ref="contactsModal" static no-padding centered size="xl" color="teal-2">
    <template #header>{{trans('ruby::words.staff')}} - @{{ openDepartment.name }}</template>
    <vue-datafilter :cols="4" datatable-ref="contactsTable">
        <dt-filter name="name" type="text" label="{{trans('common::words.name')}}"></dt-filter>
        <dt-filter name="emails.address" type="text" label="{{trans('common::words.email')}}"></dt-filter>
        <dt-filter name="phones.number" type="text" label="{{trans('common::words.phone')}}"></dt-filter>
        <dt-filter name="roles" type="select2" multiple :values="roles" label="{{trans('common::words.role')}}"></dt-filter>
        <dt-filter name="job" type="text" label="{{trans('ruby::contacts.job')}}"></dt-filter>
        <dt-filter name="applicant.recruited_at" type="date" label="{{trans('ruby::applicants.recruited_at')}}"></dt-filter>
    </vue-datafilter>

    <x-ruby::tables.contacts in-modal/>
</modal>