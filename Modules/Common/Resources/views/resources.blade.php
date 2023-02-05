<link rel="stylesheet" href="{{asset('css/fontawesome.min.css')}}" type="text/css">
<link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" type="text/css">
<link rel="stylesheet" href="{{asset(mix('css/colors.css'))}}" type="text/css">
@bukStyles(true)
<script type="text/javascript" src="{{asset('js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/bootbox.min.js')}}"></script>
<script type="text/javascript" src="{{asset(mix('js/manifest.js'))}}"></script>
<script type="text/javascript" src="{{asset(mix('js/vue.min.js'))}}"></script>
@bukScripts(true)
<script type="text/javascript">
    var locale = {};
    $.ajaxSettings.headers = {'X-CSRF-TOKEN': '{{csrf_token()}}'};
</script>