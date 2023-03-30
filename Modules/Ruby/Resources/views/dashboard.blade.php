@extends('ruby::master')

@section('title')
    <title>Ruby: Overview</title>
@stop

@push('styles')
    <link rel="stylesheet" href="{{asset('css/daterangepicker.min.css')}}" type="text/css">
@endpush

@section('content')

@stop

@push('scripts')
    <script type="text/javascript" src="{{asset('js/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/daterangepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/chart.min.js')}}"></script>
    <script type="text/javascript" src="{{asset(mix('js/ruby/dashboard.js'))}}"></script>
@endpush
