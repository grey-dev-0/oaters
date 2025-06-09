@extends('ruby::master')

@section('title')
    <title>OATERS: {{__('common::words.structure')}}</title>
@stop

@section('content')
    <div class="row">
        <breadcrumb>
            <bc-item url="{{url('r')}}">Ruby</bc-item>
            <bc-item active>{{trans('common::words.structure')}}</bc-item>
        </breadcrumb>
    </div>

    <div class="row">
        <div class="col">
            <card title="{{trans('common::words.structure')}}" color="brown-4">
                <org-chart ref="chart" :nodes="members"></org-chart>
            </card>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let subordinates = @json($subordinates);
    </script>

    @vite('resources/js/ruby/structure.js')
@endpush