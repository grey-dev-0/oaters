@extends('ruby::master')

@section('title')
    <title>OATERS: Staff</title>
@stop

@section('content')
    <div class="row">
        <breadcrumb>
            <bc-item url="{{url('sa')}}">Ruby</bc-item>
            <bc-item active>{{trans('ruby::words.staff')}}</bc-item>
        </breadcrumb>
    </div>

    <div class="row">
        <div class="col">
            <card title="{{trans('ruby::words.staff')}}" color="teal-2">
                <div class="card-body">
                    <timeline>
                        <timeline-entry>
                            <h3>First Entry</h3>
                            <p>Some Testing text<br/>With multiline input</p>
                        </timeline-entry>
                        <timeline-entry :color="test" @click="change">
                            <p>Second Entry</p>
                        </timeline-entry>
                        <timeline-entry color="amber-2">
                            <p>Third Entry</p>
                        </timeline-entry>
                        <timeline-entry>
                            <p>Fourth Entry</p>
                        </timeline-entry>
                    </timeline>
                </div>
            </card>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/ruby/contacts.js')
@endpush