<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title> @yield('title') </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- App core CSS -->
    <link href="{{ asset('/css/font-awesome.css') }}" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">

    <!-- Endless -->
    <link href="{{ asset('/css/endless.min.css') }}" rel="stylesheet">
</head>
<body>
    <div class="login-wrapper">
        <div class="text-center">
            <h2 class="fadeInUp animation-delay8" style="font-weight:bold">
                <span class="text-success">Measurement Tool</span> <span style="color:#ccc; text-shadow:0 1px #fff">Login</span>
            </h2>
        </div>
        @yield('content')
	</div><!-- /login-wrapper -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <!-- Jquery -->
    <script src="{{ asset('/js/jquery-1.10.2.min.js') }}"></script>

    <!-- Bootstrap -->
    <script src="{{ asset('/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- Modernizr -->
    <script src="{{ asset('/js/modernizr.min.js') }}"></script>

    <!-- Pace -->
    <script src="{{ asset('/js/pace.min.js') }}"></script>

    <!-- Popup Overlay -->
    <script src="{{ asset('/js/jquery.popupoverlay.min.js') }}"></script>

    <!-- Slimscroll -->
    <script src="{{ asset('/js/jquery.slimscroll.min.js') }}"></script>

    <!-- Cookie -->
    <script src="{{ asset('/js/jquery.cookie.min.js') }}"></script>

    <!-- Endless -->
    <script src="{{ asset('/js/endless/endless.js') }}"></script>
    @yield('script')
</body>
</html>
