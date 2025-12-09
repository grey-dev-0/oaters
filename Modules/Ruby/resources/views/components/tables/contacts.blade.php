@props(['inModal' => false])

<vue-datatable datatable-id="contacts-table" ref="contactsTable" @if($inModal) deferred url="{{route('ruby::contacts.index')}}" :ajax-data="{department: openDepartment.id}" @else :ajax-complete="renderActions" @endif>
    <dt-column name="name" data="name">{{trans('common::words.name')}}</dt-column>
    <dt-column name="emails.address" data="emails.0.address">{{trans('common::words.email')}}</dt-column>
    <dt-column name="phones.number" data="phones.0.number">{{trans('common::words.phone')}}</dt-column>
    <dt-column name="roles" data="roles.0.title" :searchable="false">{{trans('common::words.role')}}</dt-column>
    <dt-column name="job" data="job">{{trans('ruby::contacts.job')}}</dt-column>
    @if(!$inModal)
        <dt-column name="departments" :data="renderDepartments" :searchable="false" :orderable="false">{{trans('common::words.departments')}}</dt-column>
    @endif
    <dt-column name="applicant.recruited_at" data="applicant.recruited_at" :searchable="false">{{trans('ruby::applicants.recruited_at')}}</dt-column>
    @if(!$inModal)
        <dt-column :orderable="false" :searchable="false" class-name="nowrap" name="actions" data="actions">{{trans('common::words.actions')}}</dt-column>
    @endif
</vue-datatable>