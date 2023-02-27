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
        <nav-item url="{{url('rm/personnel')}}">{{trans('ruby::words.personnel')}}</nav-item>
        <nav-item url="{{url('rm/payroll')}}">{{trans('ruby::words.payroll')}}</nav-item>
        <nav-item url="{{url('rm/notices')}}">{{trans('ruby::words.notices')}}</nav-item>
        <nav-item url="{{url('rm/leaves')}}">{{trans('ruby::words.leaves')}}</nav-item>
        <nav-item url="{{url('rm/attendance')}}">{{trans('ruby::words.attendance')}}</nav-item>
        <nav-item url="{{url('rm/recruitment')}}">{{trans('ruby::words.recruitment')}}</nav-item>
        <template #right>
            <nav-item>
                <template #label>{{auth('admin')->user()->name}}</template>
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
