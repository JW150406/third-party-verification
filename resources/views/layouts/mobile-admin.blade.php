<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/png">


    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/jquery-ui-1.10.3.custom.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-icons/entypo/css/entypo.css') }}">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('js/select2/select2-bootstrap.css')}}">
    <link rel="stylesheet" href="{{ asset('js/select2/select2.css')}}">
    <link rel="stylesheet" href="{{ asset('js/icheck/skins/minimal/_all.css') }}">
    <link rel="stylesheet" href="{{ asset('js/icheck/skins/square/_all.css') }}">
    <link rel="stylesheet" href="{{ asset('js/icheck/skins/flat/_all.css') }}">
    <link rel="stylesheet" href="{{ asset('js/icheck/skins/futurico/futurico.css') }}">
    <link rel="stylesheet" href="{{ asset('js/icheck/skins/polaris/polaris.css') }}">
    <link rel="stylesheet" href="{{ asset('js/daterangepicker/daterangepicker-bs3.css') }}">
    <link rel="stylesheet" href="{{ asset('js/rickshaw/rickshaw.min.css')}}">
    <link rel="stylesheet" href="{{ asset('js/datatables/datatables.css')}}">
    <link rel="stylesheet" href="{{ asset('css/neon-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/neon-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/neon-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style_new.css') }}?v=9">
    <link rel="stylesheet" href="{{ asset('css/css/hover.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/progress-radial.css') }}">

    <script src="{{ asset('js/jquery-1.11.3.min.js')}}"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>

<script src="{{ asset('js/analytics.js')}}"></script>


@stack('styles')
<script src="https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC49bXfihl4zZqjG2-iRLUmcWO_PVcDehM&sensor=true&sensor=true&v=3"></script>
</head>

<body class="page-body  page-fade" data-url="{{ url('/') }}">
<!-- Header  -->
<div class="preloader"><img src="{{asset('images/loader.svg')}}" alt="{{ config('app.name', 'Laravel') }}"/></div>
<div class="ajax-loader"><img src="{{asset('images/table-loader.svg')}}" alt="{{ config('app.name', 'Laravel') }}" style="height: 30px" />
</div>
<div class="header main-header" style="padding: 0px !important;">
    @auth
    <div class="container-fluid">
        <div class="row"></div>
        @php $route = route('dashboard'); @endphp
        @if($user->access_level == 'salescenter')
            @php $route = route('dashboard',['type'=>base64_encode("salescenter"),'sid'=>base64_encode(Auth::user()->salescenter_id),'cid'=>base64_encode(Auth::user()->client_id)]); @endphp
        @endif
    </div>
    @endauth
</div>

<!-- end header -->

<div class="wrapper main-content" style="margin-top: 0px;">

    @yield('content')

</div>

<script src="{{ asset('js/datatables/datatables.js')}}"></script>
<script src="{{ asset('js/datatables/ColReorderWithResize.js')}}"></script>
<script src="{{ asset('js/datatables/rowsGroup.js')}}"></script>
<script src="{{ asset('js/datatables/dataTables.hideEmptyColumns.js')}}"></script>
<script src="{{ asset('js/bootstrap.js') }}"></script>
<script src="{{ asset('js/jquery.scrollbar.js') }}"></script>
<script src="{{ asset('js/gsap/TweenMax.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js') }}"></script>


<script src="{{ asset('js/joinable.js')}}"></script>
<script src="{{ asset('js/resizeable.js') }}"></script>
<script src="{{ asset('js/neon-api.js')}}"></script>
<script src="{{ asset('js/jquery.validate.min.js')}}"></script>
<script src="{{ asset('js/additional-methods.min.js')}}"></script>
<script src="{{ asset('js/rickshaw/vendor/d3.v3.js')}}"></script>


<script src="{{ asset('js/select2/select2.min.js')}}"></script>
<script src="{{ asset('js/icheck/icheck.min.js')}}"></script>

<script src="{{ asset('js/bootstrap-datepicker.js')}}"></script>
<script src="{{ asset('js/rickshaw/rickshaw.min.js')}}"></script>
<script src="{{ asset('js/raphael-min.js')}}"></script>
<script src="{{ asset('js/morris.min.js')}}"></script>
<script src="{{ asset('js/jquery.peity.min.js')}}"></script>
<script src="{{ asset('js/jquery.sparkline.min.js')}}"></script>
<script src="{{ asset('js/moment.min.js')}}"></script>
<script src="{{ asset('js/daterangepicker/daterangepicker.js')}}"></script>

<script src="{{ asset('js/newmultiselect/jquery.multi-select.min.js')}}"></script>
<script src="{{ asset('js/newmultiselect/multi-select-custom.js')}}"></script>


<script src="{{ asset('js/jquery.nestable.js')}}"></script>
<script src="{{ asset('js/jquery.gauge.js')}}"></script><!-- dashboard slider -->

<!-- JavaScripts initializations and stuff -->
<script src="{{ asset('js/neon-custom.js')}}"></script>


<!-- Demo Settings -->
<script src="{{ asset('js/neon-demo.js')}}"></script>
<!-- Scripts -->

<script src="{{ asset('js/admin.js') }}"></script>


<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/parsley.min.js') }}"></script>
@stack('scripts')
</body>

</html>
