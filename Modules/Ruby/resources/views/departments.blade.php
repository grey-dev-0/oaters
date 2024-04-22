@extends('ruby::master')

@section('title')
    <title>OATERS: Departments</title>
@stop

@section('content')
    <div class="row">
        <breadcrumb>
            <bc-item url="{{url('sa')}}">Ruby</bc-item>
            <bc-item active>{{trans('common::words.departments')}}</bc-item>
        </breadcrumb>
    </div>

    <div class="row">
        <div class="col">
            <card title="{{trans('common::words.departments')}}" color="indigo-3">
                <template #toolbar>
                    <div class="btn btn-sm btn-success" title="{{trans('ruby::departments.new')}}" @click="addDepartment"><i class="fa fas fa-plus align-middle"></i><span class="d-none d-sm-inline-block ml-2">{{trans('ruby::departments.new')}}</span></div>
                </template>

                <vue-datafilter :cols="4" datatable-ref="departmentsTable">
                    <dt-filter name="translations.name" type="text" label="{{trans('common::words.name')}}"></dt-filter>
                    <dt-filter name="head.name" type="text" label="{{trans('ruby::departments.head')}}"></dt-filter>
                    <dt-filter name="created_at" type="date" label="{{trans('common::words.created_at')}}" :options="{opens: 'left'}"></dt-filter>
                </vue-datafilter>

                <vue-datatable datatable-id="departments-table" ref="departmentsTable">
                    <dt-column name="translations.name" data="translations.0.name">{{trans('common::words.name')}}</dt-column>
                    <dt-column name="head.name" data="head">{{trans('ruby::departments.head')}}</dt-column>
                    <dt-column name="subordinates_count" data="subordinates_count" :searchable="false">{{trans('common::words.members')}}</dt-column>
                    <dt-column name="vacancies_count" data="vacancies_count" :searchable="false">{{trans('ruby::words.vacancies')}}</dt-column>
                    <dt-column name="created_at" data="created_at" :searchable="false">{{trans('common::words.created_at')}}</dt-column>
                    <dt-column :orderable="false" :searchable="false" class-name="nowrap" name="actions" :data="null">
                        {{trans('common::words.actions')}}
                        <template #actions>
                            <!-- TODO: Department Actions -->
                        </template>
                    </dt-column>
                </vue-datatable>
            </card>
        </div>
    </div>

    <x-ruby::modals.department-form ref="createDepartment" id="add-department" color="green-2" title="{{trans('ruby::departments.new')}}"/>
    <x-ruby::modals.department-form ref="updateDepartment" id="edit-department" color="blue-3" title="{{trans('ruby::departments.edit')}}" :edit="true"/>
@stop

@push('scripts')
    <script type="text/javascript">
        @php
        $locale = \Arr::only(trans('common::words'), ['yes', 'no', 'na', 'unpaid']) + [
            'modules' => trans('ruby::common.modules')
        ];
        @endphp
        locale.common = @json($locale);
    </script>
    @vite(['resources/js/ruby/departments.js'])
@endpush