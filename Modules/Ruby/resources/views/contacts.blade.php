@extends('ruby::master')

@section('title')
    <title>OATERS: Staff</title>
@stop

@section('content')
    <div class="row">
        <breadcrumb>
            <bc-item url="{{url('sa')}}">Ruby</bc-item>
            <bc-item active>{{trans('ruby::words.staff')}}</bc-item>
        </breadcrumb>
    </div>

    <div class="row">
        <div class="col">
            <alert id="toast" ref="toast" :color="toast.color" :content="toast.content"></alert>

            <card title="{{trans('ruby::words.staff')}}" color="teal-2">
                <template #toolbar>
                    <div class="btn btn-sm btn-success" title="{{trans('ruby::contacts.new')}}" @click="addContact"><i class="fa fas fa-plus align-middle"></i><span class="d-none d-sm-inline-block ml-2">{{trans('ruby::contacts.new')}}</span></div>
                </template>

                <vue-datafilter :cols="4" datatable-ref="contactsTable">
                    <dt-filter name="name" type="text" label="{{trans('common::words.name')}}"></dt-filter>
                    <dt-filter name="emails.address" type="text" label="{{trans('common::words.email')}}"></dt-filter>
                    <dt-filter name="phones.number" type="text" label="{{trans('common::words.phone')}}"></dt-filter>
                    <dt-filter name="roles.translations.title" type="select2" multiple :values="roles" label="{{trans('common::words.role')}}"></dt-filter>
                    <dt-filter name="departments.translations.name" type="select2" multiple :values="departments" label="{{trans('common::words.departments')}}"></dt-filter>
                    <dt-filter name="applicant.recruited_at" type="date" label="{{trans('ruby::applicants.recruited_at')}}"></dt-filter>
                </vue-datafilter>

                <x-ruby::tables.contacts/>
            </card>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let roles = @json($roles), departments = @json($departments);
    </script>
    @vite('resources/js/ruby/contacts.js')
@endpush