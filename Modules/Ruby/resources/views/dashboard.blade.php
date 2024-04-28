@extends('ruby::master')

@section('title')
    <title>Ruby: Overview</title>
@stop

@section('content')
    <card style="min-height:50vh" title="WIP">
        <v-loader></v-loader>
    </card>
@stop

@push('scripts')
    @vite(['resources/js/ruby/dashboard.js'])
@endpush
