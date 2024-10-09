<!doctype html>
<html lang="{{str_replace('_','-',app()->getLocale())}}">
<head>
    <title>Iran Tech Scrum</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <base href="{{url('/')}}">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>
<body class="rtl">

<div id="app">
    <router-view></router-view>
</div>


<script>
    window.Laravel = {};
    window.Laravel.baseUrl = '{{url('/')}}';
    window.Laravel.Auth = ('{{Auth::check()}}' !== '');
    window.Laravel.csrfToken = '{{ csrf_token() }}';
</script>

<script src="{{asset('js/manifest.js')}}"></script>
<script src="{{asset('js/vendor.js')}}"></script>
<script src="{{asset('js/app.js')}}"></script>

<script>console.log('{{app()->version()}}');</script>
</body>
</html>
