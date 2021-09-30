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
    <link rel="stylesheet" href="{{ asset('css/jquery.scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tpvagent.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toastr.css') }}">

    <link rel="stylesheet" href="{{ asset('js/dropzone/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style_new.css') }}?v=9">

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-1.11.3.min.js')}}"></script>
    <script src="{{ asset('js/parsley.min.js') }}"></script>
    <script src="{{ asset('js/admin-custom.js') }}"></script>
    <script src="{{ asset('js/analytics.js')}}"></script>
    <style type="text/css">
        .ajax-loader {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 99999;
            text-align: center;
            top: 200px;
            display: none;
        }

        .refresh-btn {
            background-color: #ffffff00; 
            cursor: pointer;
            margin-right: 0px;
            color: #ffffff;
            border: 2px solid #ffffff;
        }
        .refresh-btn:hover {
            color: #ffffff;
        }
    </style>
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
                email: '{{\Auth::user()->email}}',
            })
        }
    </script>
    <script type="text/javascript">
        var logout = false;
        /* window.addEventListener('beforeunload', (event) => {
           if (logout == false) {
             clearUseronclose();
             event.returnValue = 'You want to leave this page?';
           }

         });*/
        $(document).ready(function() {
            FreshworksWidget('hide','launcher');

            $('.assigned_workspace_nav li').trigger('click');


            $('body').on('click', '.profile-info a', function() {
                logout = true;
            });

            $('body').on('click', '.refresh-btn', function() {
                window.location.reload();
            });
        });
    </script>

    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC49bXfihl4zZqjG2-iRLUmcWO_PVcDehM&libraries=places"></script>

</head>
<!--onbeforeunload="return clearUseronclose();" -->

<body class="page-body  page-fade" data-url="{{ url('/') }}" onload="FreshworksWidget('hide','launcher');">

<div class="preloader"><img src="{{asset('images/loader.svg')}}" alt="{{ config('app.name', 'Laravel') }}" /></div>
<div class="ajax-loader"><img src="{{asset('images/table-loader.svg')}}" alt="{{ config('app.name', 'Laravel') }}" style="height:30px; width:auto;" /></div>
<!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->


<div class="header main-header pd0">
    <div class="row margin-0">
        <!-- row -->
        <!-- Profile Info and Notifications -->
        <div class="col-md-12 call-page-header">

            <div class="col-xs-6 col-sm-6 col-md-6  user-info pull-left pull-none-xsm">

                <!-- Profile Info -->
                <li>
                    <!-- add class "pull-right" if you want to place this from right -->

                    <a href="{{ url('/') }}" class="call-logo">
                        <img src="{{asset('images/logo.png')}}" alt="{{ config('app.name', 'Laravel') }}" />
                    </a>
                </li>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-6 login login-info-admin">
                <!--  <ul class="nav inline upper-qua inline-block hidden-xs">
                         <li><a href="https://app.ytica.com/fd459618-3f9d-5d78-ab40-14ff77286d01/dashboards/id/a35dfe380e9b" target="_blank">Quality</a></li>
                         </ul> -->
                <div class="text-right pdtr">
                    @permission('support')
                    <div class="inline-block hidden-xs">
                        <button class="btn btn-green btn-support" onclick="openWidget();" id="helpDeskSupportBtn"> Support </button>
                    </div>
                    @endpermission
                    <div class="loginimg inline-block hidden-xs">
                        @if(Auth::user()->profile_picture !="")
                            <img src="{{ Storage::disk('s3')->url(Auth::user()->profile_picture) }}" class="img-circle" width="44" />
                        @else
                            <?php
                            $firstName  = ucfirst(Auth::user()->first_name[0]);
                            $lastName  = ucfirst(Auth::user()->last_name[0]);
                            ?>
                            <div id="profileImage">{{ $firstName .''.$lastName}}</div>
                            {{--<img src="{{ asset('images/thumb-1@2x.png')}}" alt="" class="img-circle" width="44" />--}}
                        @endif

                    </div>
                    <div class="dropdown inline-block">
                        <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> {{ Auth::user()->full_name }}</button>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('tpvagents.edit-profile')}}">Profile</a></li>
                            <li> <a class="dropdown-profile-data" href="javascript:void(0)" data-toggle="modal" data-target="#logout_popup">
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
    </div><!-- End row -->
</div>
@if(isset($assignedclients))
    <div class="main-content">

        <div class="assigned-workspace  col-md-12 clearfix">
            <div class="assigned_workspace">
                <h3 class="table-cell first_element assigned_workspace_title">Assigned Clients</h3>
                <div class="table-cell">
                    <ul class="nav assigned_workspace_nav ">
                        <script>
                            window["workspace_name"] = {};
                            window["workspace_client"] = {};
                        </script>
                        <?php $client_name = ""; $count = 0; ?>
                        @foreach($assignedclients as $key => $assignedclient)
                            <script>
                                window["workspace_name"]["{{ $assignedclient->workflow_id }}"] = "{{ $assignedclient->name }}";
                                window["workspace_client"]["{{ $assignedclient->workflow_id }}"] = "{{ $assignedclient->client_id }}";
                            </script>
                            @if($client_name != $assignedclient->name )
                                <?php $client_name = $assignedclient->name; ?>
                                <li class="btn btn-green cursor-auto">{{ $assignedclient->name }}</li>
                            @endif
                            <?php $count++; ?>
                        @endforeach
                    </ul>
                    <div class="active-client-call"> </div>

                </div>

            </div>
        </div>
        @endif
        <hr />

        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">

                </div>
                <div class="row">
                    <main class="py-4">
                        @yield('content')
                        <div class="main-footer">
                            © {!! date('Y') !!} <strong>TPV360</strong> | All Rights Reserved
                        </div>
                    </main>
                </div>
            </div>
        </div>

    </div> <!-- Main content -->

    </div>



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


    <script src="{{ asset('js/jquery.nestable.js')}}"></script>

    <!-- JavaScripts initializations and stuff -->
    <script src="{{ asset('js/neon-custom.js')}}"></script>


    <!-- Demo Settings -->
    <script src="{{ asset('js/neon-demo.js')}}"></script>
    <!-- Scripts -->
    <script src="{{ asset('js/easytimer.min.js') }}"></script>
    <script src="{{ asset('js/toastr.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="{{ asset('js/dropzone/dropzone.min.js')}}"></script>
    <script src="{{ asset('js/dropzone/scripts.js')}}"></script>

    <!-- for tpv agent custom js -->
    <script src="{{ asset('js/tpv-agent.js') }}?v=9"></script>

    <script>



        jQuery(window).on('load', function () {
            setTimeout(function () {
                jQuery('.preloader').hide();
            }, 1000);

        });

        setTimeout(() => {
            $('.alert').hide();
        }, 3000);
        var toast = new Toastr({
          //theme: 'ocean',
          position: 'topCenter',
          autohide: false
        });
        setInterval(function() {
            $.post('/logout-inactive-tpv-agent', {
                "_token": "{{ csrf_token() }}",
            }, function(data) {
                if (data.logout === true) {
                    location.reload();
                }
                else
                {
                    toast.hide();
                }

            }).fail(function(jqXhr, textStatus, errorThrown) {
                    console.log(jqXhr);
                    console.log(jqXhr.status);
                    console.log(textStatus);
                    console.log(errorThrown);
                    if( jqXhr.status == 419 ) {
                        location.reload();
                    } else {
                        toast.show('Connection Lost... <button class="btn btn-xs refresh-btn"><b>Refresh</b></button>');
                    }
            });

        }, 10000);


        jQuery(document).ready(function() {
            jQuery('.scrollbar-inner').scrollbar();
        });


    </script>
    @stack('scripts')

</body>

</html>
