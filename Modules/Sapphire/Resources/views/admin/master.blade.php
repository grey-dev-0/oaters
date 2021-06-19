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
</head>
<body>
<div id="app">
    <navbar brand="OATERS" home="{{url('sa')}}" scheme="dark" bg-color="blue-2">
        <nav-item url="{{url('sa/tenants')}}">{{trans('sapphire::admin.tenants.title')}}</nav-item>
        <nav-item url="{{url('sa/subscriptions')}}">{{trans('sapphire::admin.subscriptions.title')}}</nav-item>
        <nav-item url="{{url('sa/payments')}}">{{trans('sapphire::admin.payments.title')}}</nav-item>
        <template v-slot:right>
            <nav-item>
                <template v-slot:label>{{auth('admin')->user()->name}}</template>
                <nav-item in-dropdown url="{{url('sa/logout')}}">{{trans('sapphire::admin.login.logout')}}</nav-item>
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
@stack('locale-scripts')
@stack('scripts')
</body>
</html>
