@extends('ruby::master')

@section('title')
    <title>OATERS: {{trans('ruby::words.attendance')}}</title>
@stop

@section('content')
    <div class="row">
        <breadcrumb>
            <bc-item url="{{url('r')}}">Ruby</bc-item>
            <bc-item active>{{trans('ruby::words.attendance')}}</bc-item>
        </breadcrumb>
    </div>

    <div class="row">
        <div class="col">
            <alert id="toast" ref="toast" :color="toast.color" :content="toast.content"></alert>

            <card title="{{trans('ruby::words.attendance')}}" color="green-1">
                <vue-datafilter :cols="4" datatable-ref="attendanceTable">
                    <dt-filter name="contact.name" type="text" label="{{trans('common::words.name')}}"></dt-filter>
                    <dt-filter name="contact.roles.title" type="select2" multiple :values="roles" label="{{trans('common::words.role')}}"></dt-filter>
                    <dt-filter name="contact.departments.name" type="select2" multiple :values="departments" label="{{trans('common::words.departments')}}"></dt-filter>
                    <dt-filter name="r_punches.created_at" type="date" label="{{trans('common::words.created_at')}}"></dt-filter>
                    <dt-filter name="type" type="select" :values="{in: '{{__('common::words.in')}}', out: '{{__('common::words.out')}}'}" label="{{__('common::words.type')}}"></dt-filter>
                </vue-datafilter>

                <vue-datatable datatable-id="attendance-table" ref="attendanceTable" :sort="[[4, 'desc']]">
                    <dt-column name="contact.name" data="contact.name">{{trans('common::words.name')}}</dt-column>
                    <dt-column name="contact.roles.title" data="contact.roles.0.title" :searchable="false">{{trans('common::words.role')}}</dt-column>
                    <dt-column name="contact.departments.name" :data="renderDepartments" :searchable="false" :orderable="false">{{trans('common::words.departments')}}</dt-column>
                    <dt-column name="type" data="type">{{trans('common::words.type')}}</dt-column>
                    <dt-column name="r_punches.created_at" data="created_at" :searchable="false">{{trans('common::words.created_at')}}</dt-column>
                    <dt-column :orderable="false" :searchable="false" class-name="nowrap" name="actions" :data="null">
                        {{trans('common::words.actions')}}
                        <template #actions>
                            <span class="text-primary profile mr-1" role="button" title="{{trans('common::words.profile')}}"><i class="fas fa-lg fa-address-card"></i></span>
                        </template>
                    </dt-column>
                </vue-datatable>
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
    @vite('resources/js/ruby/attendance.js')
@endpush
