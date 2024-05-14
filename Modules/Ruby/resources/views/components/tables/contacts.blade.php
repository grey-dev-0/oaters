@props(['inModal' => false])

<vue-datatable datatable-id="contacts-table" ref="contactsTable" @if($inModal) deferred url="{{route('ruby::contacts.index')}}" :ajax-data="{department: openDepartment.id}" @endif>
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
        <dt-column :orderable="false" :searchable="false" class-name="nowrap" name="actions" :data="null">
            {{trans('common::words.actions')}}
            <template #actions>
                <span class="text-primary profile mr-1" role="button" title="{{trans('common::words.profile')}}"><i class="fas fa-lg fa-address-card"></i></span>
                <span class="text-success edit mr-1" role="button" title="{{trans('common::words.edit')}}"><i class="far fa-lg fa-edit"></i></span>
                <span class="text-danger delete" role="button" title="{{trans('common::words.delete')}}"><i class="fas fa-lg fa-trash-can"></i></span>
            </template>
        </dt-column>
    @endif
</vue-datatable>