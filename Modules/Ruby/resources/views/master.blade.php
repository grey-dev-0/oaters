<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @yield('title')
    @include('common::resources')
    @stack('styles')
    <script type="text/javascript">
        var baseUrl = '{{url('r')}}';
    </script>
</head>
<body class="bg-grey-8">
<div id="app">
    <navbar brand="Ruby" home="{{url('r')}}" scheme="dark" bg-color="red-4">
        <nav-item>
            <template #label>{{trans('common::words.organization')}}</template>
            <nav-item in-dropdown url="{{url('r/departments')}}">{{trans('common::words.departments')}}</nav-item>
            <nav-item in-dropdown url="{{url('r/staff')}}">{{trans('ruby::words.staff')}}</nav-item>
            <nav-item in-dropdown url="{{url('r/structure')}}">{{trans('common::words.structure')}}</nav-item>
        </nav-item>
        <nav-item>
            <template #label>{{trans('common::words.personnel')}}</template>
            <nav-item in-dropdown url="{{url('r/attendance')}}">{{trans('ruby::words.attendance')}}</nav-item>
            <nav-item in-dropdown url="{{url('r/notices')}}">{{trans('ruby::words.notices')}}</nav-item>
            <nav-item in-dropdown url="{{url('r/leaves')}}">{{trans('ruby::words.leaves')}}</nav-item>
            <nav-item in-dropdown url="{{url('r/visas')}}">{{trans('ruby::words.visas')}}</nav-item>
        </nav-item>
        <nav-item>
            <template #label>{{trans('ruby::words.payroll')}}</template>
            <nav-item in-dropdown url="{{url('r/components')}}">{{trans('common::words.components')}}</nav-item>
            <nav-item in-dropdown url="{{url('r/salaries')}}">{{trans('ruby::words.salaries')}}</nav-item>
            <nav-item in-dropdown url="{{url('r/payments')}}">{{trans('common::words.payments')}}</nav-item>
        </nav-item>
        <nav-item>
            <template #label>{{trans('ruby::words.recruitment')}}</template>
            <nav-item in-dropdown url="{{url('r/vacancies')}}">{{trans('ruby::words.vacancies')}}</nav-item>
            <nav-item in-dropdown url="{{url('r/applicants')}}">{{trans('ruby::words.applicants')}}</nav-item>
            <nav-item in-dropdown url="{{url('r/documents')}}">{{trans('common::words.documents')}}</nav-item>
        </nav-item>
        <template #right>
            <nav-item>
                <template #label>{{auth()->user()->contact->name}}</template>
                <nav-item in-dropdown url="{{url('s/logout')}}">{{trans('sapphire::admin.login.logout')}}</nav-item>
            </nav-item>
        </template>
    </navbar>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col">
                @yield('content')
            </div>
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>
