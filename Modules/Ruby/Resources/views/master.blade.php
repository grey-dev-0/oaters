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
        @can('authorize', 'view-personnel') <nav-item url="{{url('r/personnel')}}">{{trans('ruby::words.personnel')}}</nav-item> @endcan
        @can('authorize', 'view-payroll') <nav-item url="{{url('r/payroll')}}">{{trans('ruby::words.payroll')}}</nav-item> @endcan
        @can('authorize', 'view-notices') <nav-item url="{{url('r/notices')}}">{{trans('ruby::words.notices')}}</nav-item> @endcan
        @can('authorize', 'view-leaves') <nav-item url="{{url('r/leaves')}}">{{trans('ruby::words.leaves')}}</nav-item> @endcan
        @can('authorize', 'view-attendance') <nav-item url="{{url('r/attendance')}}">{{trans('ruby::words.attendance')}}</nav-item> @endcan
        @can('authorize', 'view-recruitment') <nav-item url="{{url('r/recruitment')}}">{{trans('ruby::words.recruitment')}}</nav-item> @endcan
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
