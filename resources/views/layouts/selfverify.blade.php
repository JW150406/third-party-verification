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
  <link rel="stylesheet" href="{{ asset('css/font-icons/entypo/css/entypo.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="{{ asset('css/neon-theme.css') }}">
  <!--   <link rel="stylesheet" href="{{ asset('css/neon-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('js/select2/select2-bootstrap.css')}}">
    <link rel="stylesheet" href="{{ asset('js/select2/select2.css')}}">  -->
  <link href="{{ asset('css/css/hover.css') }}" rel="stylesheet">
  <link href="{{ asset('css/style_new.css') }}?v=9" rel="stylesheet">
  <link href="{{ asset('css/salesagent.css') }}?v=1" rel="stylesheet">
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
  @stack('styles')
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="{{ asset('js/jquery-1.11.3.min.js')}}"></script>
  <script src="{{ asset('js/bootstrap.js') }}"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script type="text/javascript">
    window.getformscript = "{{ route('tpvagent.clientformscript')}}";
  </script>
</head>

<body>

  <div class="preloader"><img src="{{asset('images/loader.svg')}}" alt="{{ config('app.name', 'Laravel') }}" /></div>

  @include('layouts.selfverify_header')

  <div class="salesagent-wrapper">
    @yield('content')
  </div>

  <script>
    jQuery(window).on('load', function() {
      setTimeout(function() {
        jQuery('.preloader').hide();
      }, 1000);

    });
    
  </script>
  @stack('scripts')
</body>

</html>