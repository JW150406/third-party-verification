<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->

    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{ asset('js/select2/select2-bootstrap.css')}}">
    <link rel="stylesheet" href="{{ asset('js/select2/select2.css')}}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <link href="{{ asset('css/style_new.css') }}?v=9" rel="stylesheet">
    <link href="{{ asset('css/css/hover.css') }}" rel="stylesheet">

    <link href="{{ asset('css/salesagent.css') }}?v=1" rel="stylesheet">

    <script src="{{ asset('js/app.js')}}"></script>
    <script src="{{ asset('js/jquery-1.11.3.min.js')}}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
    <div id="app">
        @include('layouts.header')
        <div class="salesagent-wrapper">
            @yield('content')
        </div>



        <script src="{{ asset('js/select2/select2.min.js')}}"></script>
        <script src="{{ asset('js/custom.js')}}"></script>


        <script type="text/javascript">
            $('.selectmenu').select2();
        </script>
</body>

</html>