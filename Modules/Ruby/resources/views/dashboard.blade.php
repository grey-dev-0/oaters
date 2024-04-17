@extends('ruby::master')

@section('title')
    <title>Ruby: Overview</title>
@stop

@section('content')

@stop

@push('scripts')
    @vite(['resources/js/ruby/dashboard.js'])
@endpush
