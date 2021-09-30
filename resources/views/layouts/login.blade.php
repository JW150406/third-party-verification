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
    <link rel="stylesheet" href="{{ asset('css/jquery-ui-1.10.3.custom.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/font-icons/entypo/css/entypo.css') }}">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
	<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
	<link rel="stylesheet" href="{{ asset('css/neon-core.css') }}">
	<link rel="stylesheet" href="{{ asset('css/neon-theme.css') }}">
	<link rel="stylesheet" href="{{ asset('css/neon-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    <script src="{{ asset('js/jquery-1.11.3.min.js') }}"></script>

	<!--[if lt IE 9]><script src="{{ asset('js/ie8-responsive-file-warning.js')}}"></script><![endif]-->
	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

    
</head>
<body class="page-body login-page login-form-fall">



<!-- This is needed when you send requests via Ajax -->
<script type="text/javascript">
var baseurl = '';
</script>
    <div id="app">
        <main class="py-4">
         @yield('content')
       </main>
    </div>

    

    <!-- Scripts -->
    <script src="{{ asset('js/gsap/TweenMax.min.js') }}"></script>
	<script src="{{ asset('js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap.js') }}"></script>
	<script src="{{ asset('js/joinable.js')}}"></script>
	<script src="{{ asset('js/resizeable.js') }}"></script>
	<script src="{{ asset('js/neon-api.js')}}"></script>
	<script src="{{ asset('js/jquery.validate.min.js')}}"></script>
	<script src="{{ asset('js/neon-login.js')}}"></script>


	<!-- JavaScripts initializations and stuff -->
	<script src="{{ asset('js/neon-custom.js')}}"></script>


	<!-- Demo Settings -->
	<script src="{{ asset('js/neon-demo.js')}}"></script>
	@yield('scripts')
    
</body>
</html>
