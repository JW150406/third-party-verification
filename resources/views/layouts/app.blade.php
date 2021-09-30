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
    <link rel="stylesheet" href="{{ asset('js/dropzone/dropzone.css') }}">

	<link href="{{ asset('css/salesagent.css') }}?v=1" rel="stylesheet">


	<script src="{{ asset('js/jquery-1.11.3.min.js')}}"></script>

	<script src="{{ asset('js/analytics.js')}}"></script>

	<script>
  	window.fwSettings={
  	'widget_id':61000000794
  	};
  	!function(){if("function"!=typeof window.FreshworksWidget){var n=function(){n.q.push(arguments)};n.q=[],window.FreshworksWidget=n}}()
  </script>
  <script type='text/javascript' src='https://widget.freshworks.com/widgets/61000000794.js' async defer></script>

  <script>

    FreshworksWidget('hide', 'launcher');
    FreshworksWidget('disable', 'ticketForm', ['name']);
    function openWidget(){
      FreshworksWidget('open');
			FreshworksWidget('identify', 'ticketForm', {
			  name: '{{\Auth::user()->first_name}} {{\Auth::user()->last_name}}',
			  
			})
    }
  </script>


	<style type="text/css">
		.user_img img {
			max-width: 110px;
			height: 110px;
		}

		.header-profile-pic {
			max-width: 58px;
		}
	</style>
	@stack('styles')
</head>

<body class="page-body  page-fade" data-url="{{ url('/') }}" onload="FreshworksWidget('hide','launcher');">
	<!-- Header  -->
	<div class="preloader"><img src="{{asset('images/loader.svg')}}" alt="{{ config('app.name', 'Laravel') }}" /></div>
	<div class="ajax-loader"><img src="{{asset('images/table-loader.svg')}}" alt="{{ config('app.name', 'Laravel') }}" /></div>
	<div class="header main-header">
		<div class="container-fluid">
			<div class="row"></div>

			<div class="col-xs-12 col-sm-12 col-md-12">
				<div id="mySidenav" class="sidenav scrollbar-inner">

					<div class="logo"><a href="{{ route('my-account')}}"><img src="{{asset('images/white-logo.png')}}" alt="{{ config('app.name', 'Laravel') }}" /></a></div>

					<ul class="accordion-menu" id="accordian" >

						<li class="{{ (Request::route()->getName() == 'my-account') ? 'active current-active' : '' }}">
							<a href="{{ route('my-account') }}">
								<span class="icon"><img src="{{asset('images/dashboard.svg')}}" /></span>Dashboard
							</a>
						</li>

						<li class="{{ (Request::route()->getName() == 'client.contact' || Request::route()->getName() == 'client.contact.from' || Request::route()->getName() == 'client.contact.from_post' || Request::route()->getName() == 'client.thank-you') ? 'active current-active' : '' }}">
							<a href="{{ route('client.contact',Auth::user()->client_id) }}">
								<span class="icon"><img src="{{asset('images/lead-add.svg')}}" /></span>New Enrollment
							</a>
						</li>

						<li class="{{ (Request::route()->getName() == 'profile.leads' || Request::route()->getName() == 'profile.leaddetail') ? 'active current-active' : '' }}">
							<a href="{{ route('profile.leads') }}">
								<span class="icon"><img src="{{asset('images/admin.svg')}}" /></span>My Leads
							</a>
						</li>
					</ul>
					
				</div>

				<div class="clearfix"></div>
				<div class="col-xs-8 col-sm-6 col-md-6 left-0">
					<div id="main">
						<a class="hamburger header-menu-toggler" href="javascript:void(0)">
							<div class="patty"></div>
						</a>

					</div>
					<a href="{{ route('my-account')}}" class="visible-sm mobile-logo visible-xs"><img src="{{asset('images/logo.png')}}" alt="{{ config('app.name', 'Laravel') }}" /></a>
				</div>


				<div class="search-container"> </div>
				<!-- <form action="/action_page.php">
							      <button type="submit"><img src="{{asset('images/search.png')}}"/></button>
							      <input type="text" placeholder="type your keyword and hit enter" name="search">
						   	</form> -->
				<!-- </div>
						</div> -->
				<div class="col-xs-4 col-sm-6 col-md-6">

					<div class="col-xs-12 col-sm-12 col-md-12 login login-info-admin text-right">
						<!--  <ul class="nav inline upper-qua inline-block hidden-xs">
                   <li><a href="https://app.ytica.com/fd459618-3f9d-5d78-ab40-14ff77286d01/dashboards/id/a35dfe380e9b" target="_blank">Quality</a></li>
                   </ul> -->
						 <div class="inline-block hidden-xs">
	             <button class="btn btn-green btn-support" onclick="openWidget();" id="helpDeskSupportBtn" > Support </button>
	           </div>

						<div class="loginimg inline-block hidden-xs">
							@if(Auth::user()->profile_picture !="")
							<img src="{{ Storage::disk('s3')->url(Auth::user()->profile_picture) }}" class="header-profile-pic" />
							@else
							{{--<img src="{{asset('images/login.jpg')}}" />--}}
								<?php
								$firstName  = ucfirst(Auth::user()->first_name[0]);
								$lastName  = ucfirst(Auth::user()->last_name[0]);
								?>
								<div id="profileImage">{{ $firstName .''.$lastName}}</div>
							@endif

						</div>
						<div class="dropdown inline-block">
							<button class="btn dropdown-toggle" type="button" data-toggle="dropdown">{{ Auth::user()->full_name }}
							</button>
							<ul class="dropdown-menu">
								<li><a href="{{route('sales-user-profile')}}">Profile</a></li>
								<li><a href="javascript:void(0)" data-toggle="modal" data-target="#logout_popup">
										Log Out
									</a>
									<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
										@csrf
									</form>
								</li>

							</ul>
						</div>
					</div>
				</div>


			</div>
		</div>
	</div>

	<!-- end header -->

	<div class="wrapper main-content space-none">

		@yield('content')

		<div class="main-footer">
	   		© {!! date('Y') !!} <strong>TPV360</strong> | All Rights Reserved
		</div>
	</div>
	
	<!-- <div class="salesagent-wrapper wrapper main-content">

	</div> -->
	<div class="team-addnewmodal">
		<div class="modal fade" id="clonelead" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel"><span><?php echo getimage('images/info-modal.png'); ?></span>Clone Lead</h4>
					</div>
					<div class="modal-body">

						<div class="col-xs-12 col-sm-12 col-md-12">
							<div class="arrow-up"></div>
							<div class="modal-form">
								<div class="col-xs-12 col-sm-12 col-md-12">

									<form class="" enctype="multipart/form-data" role="form" method="POST" action="">
										{{ csrf_field() }}
										<div class="ajax-response"></div>
										<style>
											.ui-autocomplete-loading {
												background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat;
											}
										</style>
										<div class="col-xs-12 col-sm-12 col-md-12">
											<div class="form-group">
												<label for="cloneleads">Search Lead</label>
												<input name="cloneleadajax" id="cloneleadajax" class="form-control required">

											</div>
										</div>

									</form>

								</div>
							</div>
						</div>

					</div>
					<div class="modal-footer"></div>
				</div>
			</div>
		</div>
	</div>

	<script>
		$( function() {
			$("#cloneleadajax").autocomplete({
				source: function( request, response ) {
					$.getJSON( "{{ route('ajax-getleads') }}", {
						term: request.term
					},  function(data) {
						response($.map(data, function (item) {
							return {
								label: item.refrence_id,
								value: item.id,
								url: item.url,
							}
						}));
					});
				},
				search: function() {
					// custom minLength
					var term = this.value;
					if ( term.length < 2 ) {
						return false;
					}
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function (event, ui) {
					console.log( "Selected: " + ui.item.url);
					window.location.href = ui.item.url;
				}
			});
		} );
	</script>
	<!--logout--popup---start--->

	<div class="modal fade confirmation-model" id="logout_popup">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body text-center">
					<div class="mt15 text-center mb15">
						<?php echo getimage('/images/logout.svg') ?>
						<p class="logout-title">Logout</p>
					</div>
					Are you sure you want to logout?
				</div>

				<div class="modal-footer">
					<div class="btnintable bottom_btns pd0">
						<div class="btn-group">
							<a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="btn btn-green">Yes</a>
							<a type="button" class="btn btn-red" data-dismiss="modal">No</a>


						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--logout--popup---end--->



	<script src="{{ asset('js/datatables/datatables.js')}}"></script>
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


	{{--<script src="{{ asset('js/client-contact.js') }}"></script>--}}

	<!-- Demo Settings -->
	<script src="{{ asset('js/neon-demo.js')}}"></script>
	<!-- Scripts -->

	<script src="{{ asset('js/admin.js') }}"></script>

	<script src="{{ asset('js/custom.js') }}"></script>
	<script src="{{ asset('js/parsley.min.js') }}"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('js/dropzone/dropzone.min.js')}}"></script>
    <script src="{{ asset('js/dropzone/scripts.js')}}"></script>
	<script type="text/javascript">
		//$('select').select2();

		jQuery(document).ready(function() {
			jQuery('.scrollbar-inner').scrollbar();
		});
	</script>
	<script>
		window.Parsley.addValidator('maxFileSize', {
			validateString: function(_value, maxSize, parsleyInstance) {
				if (!window.FormData) {
					alert('You are making all developpers in the world cringe. Upgrade your browser!');
					return true;
				}
				var files = parsleyInstance.$element[0].files;
				return files.length != 1  || files[0].size <= 5000000;
			},
			requirementType: 'integer',
			messages: {
				en: 'This file should not be larger than %s Mb',
				fr: 'Ce fichier est plus grand que %s mb.'
			}
		});

		$(document).ready(function() {
			$('.datepicker').datepicker();
			// Timepicker
			if ($.isFunction($.fn.timepicker)) {
				$(".timepicker").each(function(i, el) {
					var $this = $(el),
						opts = {
							template: attrDefault($this, 'template', false),
							showSeconds: attrDefault($this, 'showSeconds', false),
							defaultTime: attrDefault($this, 'defaultTime', 'current'),
							showMeridian: attrDefault($this, 'showMeridian', true),
							minuteStep: attrDefault($this, 'minuteStep', 15),
							secondStep: attrDefault($this, 'secondStep', 15)
						},
						$n = $this.next(),
						$p = $this.prev();

					$this.timepicker(opts);

					if ($n.is('.input-group-addon') && $n.has('a')) {
						$n.on('click', function(ev) {
							ev.preventDefault();

							$this.timepicker('showWidget');
						});
					}

					if ($p.is('.input-group-addon') && $p.has('a')) {
						$p.on('click', function(ev) {
							ev.preventDefault();

							$this.timepicker('showWidget');
						});
					}
				});
			}

		});
		// Element Attribute Helper
		function attrDefault($el, data_var, default_val) {
			if (typeof $el.data(data_var) != 'undefined') {
				return $el.data(data_var);
			}

			return default_val;
		}

		setInterval(function () {
	        $.post('/ajax/logout-inactive-sales-agent', {
        		"_token": "{{ csrf_token() }}",
			}, function (data) {
	            if (data.logout === true) {
	                location.reload();
	            }

	        }).fail(function () {
	            // location.reload();
	        });

	    }, 10000);
	</script>

	@stack('scripts')

</body>

</html>
