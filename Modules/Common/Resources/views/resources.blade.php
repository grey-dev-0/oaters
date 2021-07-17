<link rel="stylesheet" href="{{asset('resources/css/fontawesome.min.css')}}" type="text/css">
<link rel="stylesheet" href="{{asset('resources/css/bootstrap.min.css')}}" type="text/css">
<link rel="stylesheet" href="{{asset('resources/css/colors.min.css')}}" type="text/css">
@bukStyles(true)
<script type="text/javascript" src="{{asset('resources/js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{asset('resources/js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('resources/js/bootbox.min.js')}}"></script>
<script type="text/javascript" src="{{asset('resources/js/vue.min.js')}}"></script>
<script type="text/javascript" src="{{asset('resources/js/vue-loader.min.js')}}"></script>
@bukScripts(true)
<script type="text/javascript">
    var locale = {};
    $.ajaxSettings.headers = {'X-CSRF-TOKEN': '{{csrf_token()}}'};
</script>