<!doctype html>
<html lang="{{str_replace('_','-',app()->getLocale())}}">
<head>
    <title>Iran Tech Scrum</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="{{mix('/css/app.css')}}">
</head>
<body>

<div id="app">
    {{--start of header --}}
    <vue-header></vue-header>
    {{--end of header --}}
    <main class="container py-5" role="main">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-12">
                {{--here is the most important section for view data --}}
                @auth
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Logged In</h4>
                    <p>
                        you are logged in

                    </p>
                    <p class="mb-0"></p>
                </div>
                @else
                    <form action="/login" method="post">
                        {{@method_field('post')}}
                        @csrf
                        <div class="form-group">
                            <input type="email" name="email" value="{{old('email')}}" class="form-control"
                                   placeholder="Email">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" value="" class="form-control" placeholder="Password">
                        </div>
                        <button class="btn btn-block btn-success" type="submit">ورود</button>
                    </form>
                    @endauth
                    {{--end of router view--}}
            </div>
        </div>
    </main>
    {{--start of footer --}}
    <vue-footer></vue-footer>
    {{--end of footer --}}
</div>


<script>
    window.Laravel = {};
    window.Laravel.Auth = ('{{Auth::check()}}' !== '');
    window.Laravel.csrfToken = '{{csrf_token()}}';
</script>

<script src="{{asset('js/manifest.js')}}"></script>
<script src="{{asset('js/vendor.js')}}"></script>
<script src="{{asset('js/app.js')}}"></script>

</body>
</html>
