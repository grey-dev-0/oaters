<meta name="csrf-token" content="{{csrf_token()}}">
<link rel="stylesheet" href="{{asset('css/fontawesome.min.css')}}" type="text/css">
@vite(['resources/scss/bootstrap.scss'])
<script type="text/javascript">
    var locale = {};
    // uiLocale carries the interface-language mechanism globals (spec 003);
    // pages keep pushing their own string bundles onto `locale`.
    var uiLocale = {
        lang: '{{app()->getLocale()}}',
        dir: '{{app()->getLocale() === 'ar' ? 'rtl' : 'ltr'}}',
        label: '{{trans('common::navbar.language')}}',
        switchUrl: '{{route('common::locale.switch')}}',
        datatable: @json(trans('common::datatable')),
        picker: {
            apply: '{{trans('common::navbar.apply')}}',
            clear: '{{trans('common::navbar.clear')}}'
        }
    };
</script>
