@if(app()->getLocale() === 'ar' && !file_exists(public_path('hot')))
    @php
        // The mirrored theme replaces the theme's directional rules; the
        // overrides entry fixes component/vendor styles the mirror cannot
        // reach. Both are absent in dev/hot mode, which builds no manifest.
        try {
            $theme = \Illuminate\Support\Facades\Vite::asset('resources/scss/bootstrap.scss');
            $overrides = \Illuminate\Support\Facades\Vite::asset('resources/scss/rtl-overrides.scss');
        } catch (\Exception) {
            $theme = null;
            $overrides = null;
        }
    @endphp
    @if($theme)
        <link rel="stylesheet" href="{{preg_replace('/\.css$/', '.rtl.css', $theme)}}">
    @endif
    @if($overrides)
        <link rel="stylesheet" href="{{$overrides}}">
    @endif
@endif
