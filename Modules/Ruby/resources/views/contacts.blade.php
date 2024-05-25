@extends('ruby::master')

@section('title')
    <title>OATERS: {{trans('ruby::words.staff')}}</title>
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
                    <dt-filter name="roles" type="select2" multiple :values="roles" label="{{trans('common::words.role')}}"></dt-filter>
                    <dt-filter name="job" type="text" label="{{trans('ruby::contacts.job')}}"></dt-filter>
                    <dt-filter name="departments" type="select2" multiple :values="departments" label="{{trans('common::words.departments')}}"></dt-filter>
                    <dt-filter name="applicant.recruited_at" type="date" label="{{trans('ruby::applicants.recruited_at')}}"></dt-filter>
                </vue-datafilter>

                <x-ruby::tables.contacts/>
            </card>
        </div>
    </div>

    <x-ruby::modals.profile/>
@endsection

@push('scripts')
    <script>
        @php
            $locale = [
                'common' => \Arr::only(trans('common::words'), 'unassigned'),
                'leaves' => \Arr::only(trans('ruby::leaves'), 'types')
            ];
        @endphp
        let roles = @json($roles), departments = @json($departments);
        locale = @json($locale);
    </script>
    @vite('resources/js/ruby/contacts.js')
@endpush