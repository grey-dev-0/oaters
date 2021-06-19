<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vue Components</title>
    <link rel="stylesheet" href="{{asset('resources/css/bootstrap.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('resources/css/fontawesome.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('resources/css/jquery.dataTables.min.css')}}" type="text/css">
    <script type="text/javascript" src="{{asset('resources/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/jquery.dataTables.bs4.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/lodash.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/vue.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/js/bundles/sapphire/tenants.js')}}"></script>
</head>
<body>
<div id="app">
    <navbar></navbar>
    <vue-datatable datatable-id="testing">
        <dt-column label="x"></dt-column>
        <dt-column label="y"></dt-column>
        <dt-column label="z" last></dt-column>
    </vue-datatable>
</div>
<script type="text/javascript">
    var app = Vue.createApp({});
    for(var component in tenants.default){
        app.component(component, tenants.default[component]);
    }
    app.mount('#app');
</script>
</body>
</html>
