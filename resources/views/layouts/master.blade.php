<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title> @yield('title') </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('/img/favicon.ico') }}" type="image/x-icon">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- Pace -->
    <link href="{{ asset('/css/pace.css') }}" rel="stylesheet">
    <!-- Endless -->
    <link href="{{ asset('/css/endless.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/endless-skin.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/dashboad-new.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/custom/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery-ui.theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/custom/base-themes.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/select2/select2.css') }}" rel="stylesheet">
    <style>
        #dateForm{
                 margin-left: 0px !important;
        }
    </style>
    @yield('style')
</head>
<body class="overflow-hidden">
    <!-- Overlay Div -->
    <div id="overlay" class="transparent"></div>

    <div id="wrapper" class="preload">

        @include('partial.header');
        <?php $user = Sentinel::getUser(); ?>
            @include('partial.sidebar');
        <div id="main-container">
        <div id="breadcrumb">
            <ul class="breadcrumb">
                <li>
                    <i class="fa fa-home"></i><a href="{{ URL('/') }}"> Home</a>
                </li>
                <li class="active">@yield('breadcrumbs')</li>
            </ul>
            @if(!empty($project_id))
                @if(isset($project->name))
                    {{ Session::put('project_name', $project->name) }}
                @endif
            <span class="rLabel">
                Project name :<strong> {{ Session::get('project_name') }} </strong>
            </span>
            @endif
        </div><!-- breadcrumb -->
    <div class="padding-md">
        @include('layouts.message')
        @yield('content')
        </div><!-- /padding-md -->
    </div><!-- /main-container -->
    </div>
    <a href="" id="scroll-to-top" class="hidden-print"><i class="fa fa-chevron-up"></i></a>
    @yield('modal')
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <!-- Jquery -->
    <script src="{{ asset('/js/jquery-1.10.2.min.js') }}"></script>

    <!-- JqueryUI -->
    <script src="{{ asset('/js/common/jquery-ui.min.js') }}"></script>


    <!-- Bootstrap -->
    <script src="{{ asset('/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- Modernizr -->
    <script src="{{ asset('/js/modernizr.min.js') }}"></script>

    <!-- Pace -->
    <script src="{{ asset('/js/pace.min.js') }}"></script>

    <script src="{{ asset('/js/jquery.dataTables.min.js')}}"></script>

    <!-- Popup Overlay -->
    <script src="{{ asset('/js/jquery.popupoverlay.min.js') }}"></script>

    <!-- Slimscroll -->
    <script src="{{ asset('/js/jquery.slimscroll.min.js') }}"></script>

    <!-- Cookie -->
    <script src="{{ asset('/js/jquery.cookie.min.js') }}"></script>

    <!-- Datapicker -->
    <script src="{{ asset('/js/common/data_picker.js') }}"></script>
    <script src="{{ asset('/js/common/datepicker_click_icon.js') }}"></script>

    <!-- Endless -->
    <script src="{{ asset('/js/endless/endless.js') }}"></script>
    <!-- Ckeditor -->

    <!-- CHM JS -->
    <script src="{{ asset('/js/common/chm.js') }}"></script>
    <script src="{{ asset('/js/jquery.dynatree.min.js') }}"></script>
    <!-- CHM JS -->

    <script type="text/javascript">
        baseUrl = "{{URL::to('/')}}";
        roxyFileman = '/fileman/index.html?integration=ckeditor';
        options = {
                removeDialogTabs: 'link:upload;image:Upload',
                filebrowserBrowseUrl:roxyFileman,
                filebrowserUploadUrl:roxyFileman,
                filebrowserImageBrowseUrl:roxyFileman+'&type=image'};
    </script>
    @yield('script')
</body>
</html>
