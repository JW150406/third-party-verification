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
    <link rel="stylesheet" href="{{ asset('css/jquery.multiselect.css') }}">

    <link rel="stylesheet" href="{{ asset('js/dropzone/dropzone.css') }}">




    <!-- <link rel="stylesheet" href="{{ asset('js/fileuploader/jquery.dm-uploader.min.css') }}"> -->


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">

    <script src="{{ asset('js/jquery-1.11.3.min.js')}}"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>

    <script src="{{ asset('js/analytics.js')}}"></script>




    <script>
        window.fwSettings = {
            'widget_id': 61000000794
        };
        ! function () {
            if ("function" != typeof window.FreshworksWidget) {
                var n = function () {
                    n.q.push(arguments)
                };
                n.q = [], window.FreshworksWidget = n
            }
        }()
    </script>
    <script type='text/javascript' src='https://widget.freshworks.com/widgets/61000000794.js' async defer></script>

    <script>
        FreshworksWidget('hide', 'launcher');
        FreshworksWidget('disable', 'ticketForm', ['name']);

        function openWidget() {
            FreshworksWidget('open');
            FreshworksWidget('identify', 'ticketForm', {
                name: '{{\Auth::user()->first_name}} {{\Auth::user()->last_name}}',
                email: '{{\Auth::user()->email}}',
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
    <script src="https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js"></script>
    @if(Request::route()->getName() != 'show.sales.agent.trail')
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC49bXfihl4zZqjG2-iRLUmcWO_PVcDehM&sensor=true&sensor=true&v=3">
    </script>
    @endif
</head>

<body class="page-body  page-fade" data-url="{{ url('/') }}">
    <!-- Header  -->
    <div class="preloader"><img src="{{asset('images/loader.svg')}}" alt="{{ config('app.name', 'Laravel') }}" /></div>
    <div class="ajax-loader"><img src="{{asset('images/table-loader.svg')}}" alt="{{ config('app.name', 'Laravel') }}"
            style="height: 30px" />
    </div>
    <div class="header main-header">
        @auth
        <div class="container-fluid">
            <div class="row"></div>
            @php $route = route('dashboard'); @endphp
            @if(Auth::user()->access_level == 'salescenter')
            @php $route =
            route('dashboard',['type'=>base64_encode("salescenter"),'sid'=>base64_encode(Auth::user()->salescenter_id),'cid'=>base64_encode(Auth::user()->client_id)]);
            @endphp
            @endif
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div id="mySidenav" class="sidenav scrollbar-inner">
                    <div class="logo"><a href="{{ $route }}"><img src="{{asset('images/white-logo.png')}}"
                                alt="{{ config('app.name', 'Laravel') }}" /></a>
                    </div>

                    <div class="accordion-menu" id="accordian">
                        <ul>
                            @if(Auth::user()->can('dashboard') AND Auth::user()->can('agent-dashboard') != 1)
                            <li>
                                <a class="{{ (Request::route()->getName() == 'dashboard') ? 'active current-active' : '' }}"
                                    href="{{ $route}}"><span class="icon"><img
                                            src="{{asset('images/dashboard.svg')}}" /></span>Dashboard</a>
                            </li>
                            @elseif(Auth::user()->can('agent-dashboard') AND Auth::user()->can('dashboard') != 1)
                            <li>
                                <a class="{{ (Request::route()->getName() == 'agentdashboard') ? 'active current-active' : '' }}"
                                    href="{{ route('agentdashboard') }}"><span class="icon"><img
                                            src="{{asset('images/dashboard.svg')}}" /></span>Dashboard</a>
                            </li>
                            @elseif(Auth::user()->can('agent-dashboard') AND Auth::user()->can('dashboard'))
                            <li
                                class="{{ (Request::route()->getName() == 'dashboard' || Request::route()->getName() == 'agentdashboard') ? 'active current-active' : '' }}">
                                <a href="javascript:void(0)"><span class="icon"><img
                                            src="{{asset('images/dashboard.svg')}}" /></span>Dashboard</a>
                                <ul class="menu-child-1">
                                    <li>
                                        <a class="{{ (Request::route()->getName() == 'dashboard') ? 'active current-active' : '' }}"
                                            href="{{ $route}}">Client Dashboard</a>
                                    </li>
                                    <li>
                                        <a class="{{ (Request::route()->getName() == 'agentdashboard') ? 'active current-active' : '' }}"
                                            href="{{ route('agentdashboard') }}">Agent Dashboard</a>
                                    </li>
                                </ul>
                            </li>
                            @endif

                            @if(Auth::user()->access_level == 'tpv')

                            @if(Auth::user()->can(['all-clients', 'view-client-info']))
                            <li
                                class="{{ (Request::route()->getName() == 'client.index' || Request::route()->getName() == 'client.create' ||  Request::route()->getName() == 'client.salescenter.show' || Request::route()->getName() == 'client.salescenters.edit' || Request::route()->getName() == 'client.salescenters.create'  || Request::route()->getName() == 'client.show'   || Request::route()->getName() == 'client.edit' || Request::route()->getName() == 'client.contact-page-layout' || Request::route()->getName() == 'client.create-contact-page' || Request::route()->getName() == 'client.utility.bulkupload' || Request::route()->getName() == 'admin.clients.scripts.index' || Request::route()->getName() == 'utility.programs.bulkupload' || Request::route()->getName() == 'admin.clients.import.question' || Request::route()->getName() == 'client.salesagents.bulkupload'  || Request::route()->getName() == 'disposition.bulkupload' || Request::route()->getName() == 'salescenter.user.bulkupload' || Request::route()->getName() == 'do-not-enroll.bulkupload' ) ? 'active current-active' : '' }}">

                                <a href="javascript:void(0)"><span class="icon"><img
                                            src="{{asset('images/users.svg')}}" /></span>Clients</a>
                                <ul class="menu-child-1">
                                    @if(Auth::user()->can(['all-clients']))
                                    <li><a href="{{ route('client.index') }}"
                                            class="{{ (Request::route()->getName() == 'client.index' || Request::route()->getName() == 'client.create') ? 'active current-active' : '' }}"><b>All
                                                Clients</b></a>
                                    </li>
                                    @endif
                                    <?php $clients = getAllClients(); ?>
                                    @foreach($clients as $client)
                                    <li>
                                        <a href="{{ route('client.show', $client->id) }}"
                                            class="{{ ( Request::segment(3) == $client->id  ||  Request::segment(4) == $client->id) ? 'active current-active' : '' }}">{{$client->name}}</a>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                            @endif

                            @elseif(Auth::user()->isAccessLevelToClient())
                            @if(Auth::user()->hasPermissionTo('view-client-info') ||
                            Auth::user()->hasPermissionTo('all-clients') )
                            <li
                                class="{{ (Request::route()->getName() == 'client.index' || Request::route()->getName() == 'client.create' ||  Request::route()->getName() == 'client.salescenter.show' || Request::route()->getName() == 'client.salescenters.edit' || Request::route()->getName() == 'client.salescenters.create'  || Request::route()->getName() == 'client.show'   || Request::route()->getName() == 'client.edit' || Request::route()->getName() == 'client.contact-page-layout' || Request::route()->getName() == 'client.create-contact-page' || Request::route()->getName() == 'client.utility.bulkupload' || Request::route()->getName() == 'admin.clients.scripts.index' || Request::route()->getName() == 'utility.programs.bulkupload'  || Request::route()->getName() == 'admin.clients.import.question' || Request::route()->getName() == 'client.salesagents.bulkupload'  || Request::route()->getName() == 'disposition.bulkupload' ) ? 'active current-active' : '' }}">

                                @if(Auth::user()->isAccessLevelToClient())
                                <?php $client = getAllClients()->where('id', Auth::user()->client_id)->first(); ?>
                                @if(!empty($client))
                                <a href="{{ route('client.show', $client->id) }}"
                                    class="{{ ( Request::segment(3) == $client->id ) ? 'active current-active' : '' }}"><span
                                        class="icon"><img
                                            src="{{asset('images/users.svg')}}" /></span>{{$client->name}}</a>
                                @else
                                <a href="javascript:void(0)"><span class="icon"><img
                                            src="{{asset('images/users.svg')}}" /></span>Clients</a>
                                @endif
                                @else
                                <a href="javascript:void(0)"><span class="icon"><img
                                            src="{{asset('images/users.svg')}}" /></span>Clients</a>
                                <ul class="menu-child-1">
                                    <?php $clients = getAllClients(); ?>
                                    @foreach($clients as $client)
                                    @if($client->id == Auth::user()->client_id)
                                    <li>
                                        <a href="{{ route('client.show', $client->id) }}"
                                            class="{{ ( Request::segment(3) == $client->id  ||  Request::segment(4) == $client->id) ? 'active current-active' : '' }}">{{$client->name}}</a>
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                            @endif

                            @elseif(Auth::user()->hasAccessLevels('salescenter'))
                            @if(Auth::user()->hasPermissionTo('view-client-info') ||
                            Auth::user()->hasPermissionTo('all-clients') ))
                            <li
                                class="{{ (Request::route()->getName() == 'client.index' || Request::route()->getName() == 'client.create' ||  Request::route()->getName() == 'client.salescenter.show' || Request::route()->getName() == 'client.salescenters.edit' || Request::route()->getName() == 'client.salescenters.create'  || Request::route()->getName() == 'client.show'   || Request::route()->getName() == 'client.edit' || Request::route()->getName() == 'client.contact-page-layout' || Request::route()->getName() == 'client.create-contact-page' || Request::route()->getName() == 'client.utility.bulkupload' || Request::route()->getName() == 'admin.clients.scripts.index' || Request::route()->getName() == 'utility.programs.bulkupload'  || Request::route()->getName() == 'admin.clients.import.question' || Request::route()->getName() == 'client.salesagents.bulkupload'  || Request::route()->getName() == 'disposition.bulkupload'  ) ? 'active current-active' : '' }}">
                                @if(Auth::user()->hasAccessLevels('salescenter'))
                                <?php $client = getAllClients()->where('id', Auth::user()->client_id)->first(); ?>
                                @if(!empty($client))
                                <a href="{{ route('client.show', $client->id) }}"
                                    class="{{ ( Request::segment(3) == $client->id ) ? 'active current-active' : '' }}"><span
                                        class="icon"><img
                                            src="{{asset('images/users.svg')}}" /></span>{{$client->name}}</a>
                                @else
                                <a href="javascript:void(0)"><span class="icon"><img
                                            src="{{asset('images/users.svg')}}" /></span>Clients</a>
                                @endif
                                @else
                                <a href="javascript:void(0)"><span class="icon"><img
                                            src="{{asset('images/users.svg')}}" /></span>Clients</a>
                                <ul class="menu-child-1">
                                    <?php $clients = getAllClients(); ?>
                                    @foreach($clients as $client)
                                    @if($client->id == Auth::user()->client_id)
                                    <li>
                                        <a href="{{ route('client.show', $client->id) }}"
                                            class="{{ ( Request::segment(3) == $client->id  ||  Request::segment(4) == $client->id ) ? 'active current-active' : '' }}">{{$client->name}}</a>
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                            @endif

                            @endif
                            @if(Auth::user()->isAccessLevelToClient())
                            @if(Auth::user()->hasPermissionTo('generate-lead-detail-report') || Auth::user()->hasPermissionTo('generate-enrollment-report') || Auth::user()->hasPermissionTo('generate-sales-activity-report') || Auth::user()->hasPermissionTo('generate-critical-alert-report') || Auth::user()->hasPermissionTo('generate-recordings-report'))
                            <li
                                class="{{ (Request::route()->getName() == 'reports.reportform' || Request::route()->getName() == 'admin.tpv_recording.get' || Request::route()->getName() == 'reports.sales.activity' || Request::route()->getName() == 'reports.salesreportform' || Request::route()->getName() == 'telesales.getLeads' || Request::route()->getName() == 'telesales.show' || Request::route()->getName() == 'reports.critical.alert' || Request::route()->getName() == 'critical-logs.show' || Request::route()->getName() == 'show.sales.agent.trail') || (Request::route()->getName() == 'reports.call.history' || Request::route()->getName() == 'reports.megareportform' || Request::route()->getName() == 'show.billing.report' || Request::route()->getName() == 'show.calldetails.report') ? 'active current-active' : '' }}">
                                <a href="javascript:void(0)"><span class="icon"><img
                                            src="{{asset('images/statistics.svg')}}" /></span>Analytics</a>
                                <ul class="menu-child-1">
                                    <!-- <li><a href="{{ route('reports.salesreportform') }}"
                                               class="{{ (Request::route()->getName() == 'reports.salesreportform') ? 'active current-active' : '' }}">Daily
                                                Sales Report</a></li> -->
                                    @if(Auth::user()->hasPermissionTo('generate-enrollment-report'))
                                    <li><a href="{{ route('reports.reportform') }}"
                                            class="{{ (Request::route()->getName() == 'reports.reportform') ? 'active current-active' : '' }}">Enrollment
                                            Report</a></li>
                                    @endif
                                    @if(Auth::user()->hasRole('admin') ||
                                    Auth::user()->hasPermissionTo('generate-enrollment-report') && Auth::user()->client_id ==
                                    config('constants.CLIENT_MEGA_ENERGY_ID'))
                                    <li><a href="{{ route('reports.megareportform') }}"
                                            class="{{ (Request::route()->getName() == 'reports.megareportform') ? 'active current-active' : '' }}">Daily
                                            Verified Calls - Mega</a></li>
                                    @endif
                                    @if(Auth::user()->hasPermissionTo('generate-lead-detail-report'))
                                    <li>
                                        <a href="{{route('telesales.getLeads')}}"
                                            class="{{ (Request::route()->getName() == 'telesales.getLeads' || Request::route()->getName() == 'telesales.show') ? 'active current-active' : '' }}">Lead
                                            Detail Report</a>
                                    </li>
                                    @endif
                                    @if(Auth::user()->hasPermissionTo('generate-critical-alert-report'))
                                    <li>
                                        <a href="{{route('reports.critical.alert')}}"
                                            class="{{ (Request::route()->getName() == 'reports.critical.alert' || Request::route()->getName() == 'critical-logs.show') ? 'active current-active' : '' }}">Critical
                                            Alert Report</a>
                                    </li>
                                    @endif

                                    @if(Auth::user()->hasPermissionTo('generate-recordings-report'))
                                    <li><a href="{{ route('admin.tpv_recording.get') }}"
                                            class="{{ (Request::route()->getName() == 'admin.tpv_recording.get') ? 'active current-active' : '' }}">TPV
                                            Recordings</a></li>
                                    @endif
                                    @if(Auth::user()->hasPermissionTo('generate-sales-agent-trail'))
                                    <li><a href="{{ route('show.sales.agent.trail') }}"
                                            class="{{ (Request::route()->getName() == 'show.sales.agent.trail') ? 'active current-active' : '' }}">Sales
                                            Agent Trail</a></li>
                                    @endif

                                    @if(Auth::user()->hasPermissionTo('generate-call-detail-report'))
                                    <li><a href="{{ route('show.calldetails.report') }}"
                                            class="{{ (Request::route()->getName() == 'show.calldetails.report') ? 'active current-active' : '' }}">Call
                                            Received Data</a></li>
                                    @endif

                                    <!-- @if(Auth::user()->hasPermissionTo('generate-enrollment-report')) -->
                                    <li><a href="{{ route('show.enrollment.report') }}"
                                            class="{{ (Request::route()->getName() == 'show.enrollment.report') ? 'active current-active' : '' }}">Enrollment Report- PTM</a></li>
                                    <!-- @endif -->

                                   
                                </ul>
                            </li>
                            @endif
                            @else
                            @if(Auth::user()->can(['generate-lead-detail-report','generate-enrollment-report','generate-sales-activity-report','generate-critical-alert-report',
                            'generate-recordings-report']))
                            <li
                                class="{{ (Request::route()->getName() == 'reports.reportform' || Request::route()->getName() == 'admin.tpv_recording.get' || Request::route()->getName() == 'reports.sales.activity' || Request::route()->getName() == 'reports.salesreportform' || Request::route()->getName() == 'telesales.getLeads' || Request::route()->getName() == 'telesales.show' || Request::route()->getName() == 'reports.critical.alert' || Request::route()->getName() == 'critical-logs.show' || Request::route()->getName() == 'show.sales.agent.trail') || (Request::route()->getName() == 'reports.call.history' || Request::route()->getName() == 'reports.megareportform' || Request::route()->getName() == 'show.billing.report' || Request::route()->getName() == 'show.calldetails.report') ? 'active current-active' : '' }}">
                                <a href="javascript:void(0)"><span class="icon"><img
                                            src="{{asset('images/statistics.svg')}}" /></span>Analytics</a>
                                <ul class="menu-child-1">
                                    <!-- <li><a href="{{ route('reports.salesreportform') }}"
                                               class="{{ (Request::route()->getName() == 'reports.salesreportform') ? 'active current-active' : '' }}">Daily
                                                Sales Report</a></li> -->
                                    @if(Auth::user()->can('generate-enrollment-report'))
                                    <li><a href="{{ route('reports.reportform') }}"
                                            class="{{ (Request::route()->getName() == 'reports.reportform') ? 'active current-active' : '' }}">Enrollment
                                            Report</a></li>
                                    @endif
                                    @if(Auth::user()->hasRole('admin') ||
                                    Auth::user()->can('generate-enrollment-report') && Auth::user()->client_id ==
                                    config('constants.CLIENT_MEGA_ENERGY_ID'))
                                    <li><a href="{{ route('reports.megareportform') }}"
                                            class="{{ (Request::route()->getName() == 'reports.megareportform') ? 'active current-active' : '' }}">Daily
                                            Verified Calls - Mega</a></li>
                                    @endif
                                    {{-- @if(Auth::user()->can('generate-sales-activity-report'))
                                    <li><a href="{{ route('reports.sales.activity') }}"
                                    class="{{ (Request::route()->getName() == 'reports.sales.activity') ? 'active current-active' : '' }}">Sales
                                    Activity Report</a>
                            </li>
                            @endif--}}
                            @if(Auth::user()->can('generate-lead-detail-report'))
                            <li>
                                <a href="{{route('telesales.getLeads')}}"
                                    class="{{ (Request::route()->getName() == 'telesales.getLeads' || Request::route()->getName() == 'telesales.show') ? 'active current-active' : '' }}">Lead
                                    Detail Report</a>
                            </li>
                            @endif
                            @if(Auth::user()->can('generate-critical-alert-report'))
                            <li>
                                <a href="{{route('reports.critical.alert')}}"
                                    class="{{ (Request::route()->getName() == 'reports.critical.alert' || Request::route()->getName() == 'critical-logs.show') ? 'active current-active' : '' }}">Critical
                                    Alert Report</a>
                            </li>
                            @endif

                            @if(Auth::user()->can('generate-recordings-report'))
                            <li><a href="{{ route('admin.tpv_recording.get') }}"
                                    class="{{ (Request::route()->getName() == 'admin.tpv_recording.get') ? 'active current-active' : '' }}">TPV
                                    Recordings</a></li>
                            @endif
                            @if(Auth::user()->can('generate-sales-agent-trail'))
                            <li><a href="{{ route('show.sales.agent.trail') }}"
                                    class="{{ (Request::route()->getName() == 'show.sales.agent.trail') ? 'active current-active' : '' }}">Sales
                                    Agent Trail</a></li>
                            @endif
                            @if(Auth::user()->can('generate-call-detail-report'))
                            <li><a href="{{ route('show.calldetails.report') }}"
                                    class="{{ (Request::route()->getName() == 'show.calldetails.report') ? 'active current-active' : '' }}">Call
                                    Received Data</a></li>
                            @endif
                            @if(Auth::user()->can('generate-billing-report'))
                            <li><a href="{{ route('show.billing.report') }}"
                                    class="{{ (Request::route()->getName() == 'show.billing.report') ? 'active current-active' : '' }}">Billing
                                    Duration Report</a></li>
                            @endif
                             @if(Auth::user()->can('generate-billing-report'))
                            <li><a href="{{ route('show.billing.report') }}"
                                    class="{{ (Request::route()->getName() == 'show.billing.report') ? 'active current-active' : '' }}">Billing
                                    Duration Report</a></li>
                            @endif
                            @if(Auth::user()->can('generate-sales-agent-trail'))
                            <li><a href="{{ route('reports.ptmreportform') }}"
                                    class="{{ (Request::route()->getName() == 'reports.ptmreportform') ? 'active current-active' : '' }}">Enrollment Report - PTM</a></li>
                            @endif
                        </ul>
                        </li>

                        @endif
                        @endif
                        @if(Auth::user()->can(['view-user-roles','all-users', 'view-client-user', 'view-tpv-users',
                        'view-sales-users', 'view-all-agents', 'view-sales-agents', 'view-tpv-agents']))

                        <li
                            class="{{ (Request::route()->getName() == 'teammembers.index' || Request::route()->getName() == 'tpvagents.index' || Request::route()->getName() == 'admin.sales.users' || Request::route()->getName() == 'admin.sales.agents' || Request::route()->getName() == 'admin.client-users' || Request::route()->getName() == 'admin.all.users' || Request::route()->getName() == 'admin.all.agents' || Request::route()->getName() == 'all.permissions' || Request::route()->getName() == 'external.permissions' || Request::route()->getName() == 'edit.external.permissions.roles' || Request::route()->getName() == 'edit.permissions.roles' || Request::route()->getName() == 'all.external.permissions' || Request::route()->getName() == 'get.external.permissions.roles') ? 'active current-active' : '' }}">
                            @if(Auth::user()->hasPermissionTo('view-user-roles')|| Auth::user()->hasPermissionTo('all-users') || Auth::user()->hasPermissionTo('view-client-user') || Auth::user()->hasPermissionTo('view-tpv-users') ||  Auth::user()->hasPermissionTo('view-sales-users') || Auth::user()->hasPermissionTo('view-all-agents') || Auth::user()->hasPermissionTo('view-sales-agents') || Auth::user()->hasPermissionTo('view-tpv-agents'))
                                <a href="javascript:void(0)"><span class="icon"><img
                                        src="{{asset('images/admin.svg')}}" /></span>Admin</a>

                            @endif

                            <ul class="menu-child-1">
                                @if(Auth::user()->can('view-user-roles'))
                                {{-- <a class="drop-inner" href="javascript:void(0)">User Roles</a>
                                <!-- <li><a href="{{route('all.permissions')}}"
                                class="{{ (Request::route()->getName() == 'all.permissions' || Request::route()->getName() == 'edit.permissions.roles') ? 'active current-active' : '' }}">User
                                Roles</a>
                        </li> --> --}}
                        <li
                            class="{{ (Request::route()->getName() == 'all.permissions' || Request::route()->getName() == 'external.permissions' || Request::route()->getName() == 'edit.permissions.roles' || Request::route()->getName() == 'edit.external.permissions.roles'  || Request::route()->getName() == 'all.external.permissions' || Request::route()->getName() == 'get.external.permissions.roles') ? 'active current-active' : '' }}">
                            <a class="drop-inner" href="javascript:void(0)">User Roles</a>
                            <ul class="menu-child-1">
                                <li>
                                    <a class="{{ (Request::route()->getName() == 'all.permissions' || Request::route()->getName() == 'edit.permissions.roles') ? 'active current-active' : '' }}"
                                        href="{{route('all.permissions')}}">Internal</a>
                                </li>
                                <li>
                                    <a class="{{ (Request::route()->getName() == 'external.permissions' || Request::route()->getName() == 'edit.external.permissions.roles' || Request::route()->getName() == 'all.external.permissions' || Request::route()->getName() == 'get.external.permissions.roles')  ? 'active current-active' : '' }}"
                                        href="{{ route('external.permissions') }}">External</a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @if(Auth::user()->can('update-lead-manually'))
                        <li>
                            <a href="{{route('update.lead')}}"
                                class="">Update Lead</a>
                        </li>
                        @endif



                        @if(Auth::user()->can(['all-users', 'view-client-user', 'view-tpv-users',
                        'view-sales-users', 'view-all-agents', 'view-sales-agents', 'view-tpv-agents']))
                        <li
                            class="{{ (Request::route()->getName() == 'teammembers.index' || Request::route()->getName() == 'tpvagents.index' || Request::route()->getName() == 'admin.sales.users' || Request::route()->getName() == 'admin.sales.agents' || Request::route()->getName() == 'admin.client-users' || Request::route()->getName() == 'admin.all.users' || Request::route()->getName() == 'admin.all.agents') ? 'active current-active' : '' }}">
                            @if(Auth::user()->hasPermissionTo('all-users') || Auth::user()->hasPermissionTo('view-client-user') || Auth::user()->hasPermissionTo('view-tpv-users') || Auth::user()->hasPermissionTo('view-sales-users') || Auth::user()->hasPermissionTo('view-all-agents') || Auth::user()->hasPermissionTo('view-sales-agents') || Auth::user()->hasPermissionTo('view-tpv-agents'))
                            <a class="drop-inner" href="javascript:void(0)">User Management</a>
                            @endif
                            <ul class="menu-child-2">
                                {{-- @role('admin') --}}
                                @if(Auth::user()->isAccessLevelToClient())
                                @if(Auth::user()->hasPermissionTo('all-users') || Auth::user()->hasPermissionTo('view-client-user') || Auth::user()->hasPermissionTo('view-tpv-users') || Auth::user()->hasPermissionTo('view-sales-users'))
                                <li
                                    class="{{ (Request::route()->getName() == 'teammembers.index' || Request::route()->getName() == 'admin.sales.users' || Request::route()->getName() == 'admin.client-users' || Request::route()->getName() == 'admin.all.users') ? 'active current-active' : '' }}">
                                    <a class="drop-inner" href="javascript:void(0)">Users</a>
                                    <ul>
                                        @if(Auth::user()->hasPermissionTo('all-users'))
                                        <li><a href="{{route('admin.all.users')}}"
                                                class="{{ (Request::route()->getName() == 'admin.all.users') ? 'active current-active' : '' }}">All
                                                Users</a></li>
                                        @endif
                                        @if(Auth::user()->hasPermissionTo('view-client-user'))
                                        <li><a href="{{route('admin.client-users')}}"
                                                class="{{ (Request::route()->getName() == 'admin.client-users') ? 'active current-active' : '' }}">Client
                                                Users</a></li>
                                        @endif
                                        @if(Auth::user()->hasPermissionTo('view-tpv-users'))
                                        <li><a href="{{ route('teammembers.index') }}"
                                                class="{{ (Request::route()->getName() == 'teammembers.index') ? 'active current-active' : '' }}">TPV
                                                Users</a></li>
                                        @endif
                                        @if(Auth::user()->hasPermissionTo('view-sales-users'))
                                        <li><a href="{{route('admin.sales.users')}}"
                                                class="{{ (Request::route()->getName() == 'admin.sales.users') ? 'active current-active' : '' }}">Sales
                                                Center Users</a></li>
                                        @endif
                                    </ul>
                                </li>
                                @endif
                                @else
                                @if(Auth::user()->can(['all-users', 'view-client-user', 'view-tpv-users',
                                'view-sales-users']))
                                <li
                                    class="{{ (Request::route()->getName() == 'teammembers.index' || Request::route()->getName() == 'admin.sales.users' || Request::route()->getName() == 'admin.client-users' || Request::route()->getName() == 'admin.all.users') ? 'active current-active' : '' }}">
                                    <a class="drop-inner" href="javascript:void(0)">Users</a>
                                    <ul>
                                        @if(Auth::user()->can('all-users'))
                                        <li><a href="{{route('admin.all.users')}}"
                                                class="{{ (Request::route()->getName() == 'admin.all.users') ? 'active current-active' : '' }}">All
                                                Users</a></li>
                                        @endif
                                        @if(Auth::user()->can('view-client-user'))
                                        <li><a href="{{route('admin.client-users')}}"
                                                class="{{ (Request::route()->getName() == 'admin.client-users') ? 'active current-active' : '' }}">Client
                                                Users</a></li>
                                        @endif
                                        @if(Auth::user()->can('view-tpv-users'))
                                        <li><a href="{{ route('teammembers.index') }}"
                                                class="{{ (Request::route()->getName() == 'teammembers.index') ? 'active current-active' : '' }}">TPV
                                                Users</a></li>
                                        @endif
                                        @if(Auth::user()->can('view-sales-users'))
                                        <li><a href="{{route('admin.sales.users')}}"
                                                class="{{ (Request::route()->getName() == 'admin.sales.users') ? 'active current-active' : '' }}">Sales
                                                Center Users</a></li>
                                        @endif
                                    </ul>
                                </li>
                                @endif
                                @endif
                                @if(Auth::user()->isAccessLevelToClient())
                                @if(Auth::user()->hasPermissionTo('view-all-agents') || Auth::user()->hasPermissionTo('view-sales-agents') || Auth::user()->hasPermissionTo('view-tpv-agents'))
                                <li
                                    class="{{ (Request::route()->getName() == 'tpvagents.index' || Request::route()->getName() == 'admin.sales.agents' || Request::route()->getName() == 'admin.all.agents') ? 'active current-active' : '' }}">
                                    <a class="drop-inner" href="javascript:void(0)">Agents</a>
                                    <ul class="menu-child-2">
                                        @if(Auth::user()->hasPermissionTo('view-all-agents'))
                                        <li><a href="{{route('admin.all.agents')}}"
                                                class="{{ (Request::route()->getName() == 'admin.all.agents') ? 'active current-active' : '' }}">
                                                All Agents</a></li>
                                        @endif
                                        @if(Auth::user()->hasPermissionTo('view-sales-agents'))
                                        <li><a href="{{ route('admin.sales.agents') }}"
                                                class="{{ (Request::route()->getName() == 'admin.sales.agents') ? 'active current-active' : '' }}">
                                                Sales Agents</a></li>
                                        @endif
                                        @if(Auth::user()->hasPermissionTo('view-tpv-agents'))
                                        <li><a href="{{ route('tpvagents.index') }}"
                                                class="{{ (Request::route()->getName() == 'tpvagents.index') ? 'active current-active' : '' }}">
                                                TPV Agents</a></li>
                                        @endif
                                    </ul>
                                </li>
                                @endif
                                @else
                                @if(Auth::user()->can(['view-all-agents', 'view-sales-agents','view-tpv-agents']))
                                <li
                                    class="{{ (Request::route()->getName() == 'tpvagents.index' || Request::route()->getName() == 'admin.sales.agents' || Request::route()->getName() == 'admin.all.agents') ? 'active current-active' : '' }}">
                                    <a class="drop-inner" href="javascript:void(0)">Agents</a>
                                    <ul class="menu-child-2">
                                        @if(Auth::user()->can('view-all-agents'))
                                        <li><a href="{{route('admin.all.agents')}}"
                                                class="{{ (Request::route()->getName() == 'admin.all.agents') ? 'active current-active' : '' }}">
                                                All Agents</a></li>
                                        @endif
                                        @if(Auth::user()->can('view-sales-agents'))
                                        <li><a href="{{ route('admin.sales.agents') }}"
                                                class="{{ (Request::route()->getName() == 'admin.sales.agents') ? 'active current-active' : '' }}">
                                                Sales Agents</a></li>
                                        @endif
                                        @if(Auth::user()->can('view-tpv-agents'))
                                        <li><a href="{{ route('tpvagents.index') }}"
                                                class="{{ (Request::route()->getName() == 'tpvagents.index') ? 'active current-active' : '' }}">
                                                TPV Agents</a></li>
                                        @endif
                                    </ul>
                                </li>
                                @endif
                                @endif
                                @else
                                @if(Auth::user()->isAccessLevelToClient())
                                @if(Auth::user()->hasPermissionTo('all-users'))
                                <li><a href="{{route('admin.all.users')}}"
                                        class="{{ (Request::route()->getName() == 'admin.all.users') ? 'active current-active' : '' }}">All
                                        Users</a></li>
                                @endif
                                @else
                                @if(Auth::user()->can('all-users'))
                                <li><a href="{{route('admin.all.users')}}"
                                        class="{{ (Request::route()->getName() == 'admin.all.users') ? 'active current-active' : '' }}">All
                                        Users</a></li>
                                @endif
                                @endif
                                @if(Auth::user()->isAccessLevelToClient())
                                @if(Auth::user()->hasPermissionTo('view-client-user'))
                                <li><a href="{{route('admin.client-users')}}"
                                        class="{{ (Request::route()->getName() == 'admin.client-users') ? 'active current-active' : '' }}">Client
                                        Users</a></li>
                                @endif
                                @else
                                @if(Auth::user()->can('view-client-user'))
                                <li><a href="{{route('admin.client-users')}}"
                                        class="{{ (Request::route()->getName() == 'admin.client-users') ? 'active current-active' : '' }}">Client
                                        Users</a></li>
                                @endif
                                @endif
                                @if(Auth::user()->isAccessLevelToClient())
                                @if(Auth::user()->hasPermissionTo('view-tpv-users'))
                                <li><a href="{{ route('teammembers.index') }}"
                                        class="{{ (Request::route()->getName() == 'teammembers.index') ? 'active current-active' : '' }}">TPV
                                        Users</a></li>
                                @endif
                                @else
                                @if(Auth::user()->can('view-tpv-users'))
                                <li><a href="{{ route('teammembers.index') }}"
                                        class="{{ (Request::route()->getName() == 'teammembers.index') ? 'active current-active' : '' }}">TPV
                                        Users</a></li>
                                @endif
                                @endif
                                @if(Auth::user()->isAccessLevelToClient())
                                @if(Auth::user()->hasPermissionTo('view-sales-users'))
                                <li><a href="{{route('admin.sales.users')}}"
                                        class="{{ (Request::route()->getName() == 'admin.sales.users') ? 'active current-active' : '' }}">Sales
                                        Center Users</a></li>
                                @endif
                                @else
                                @if(Auth::user()->can('view-sales-users'))
                                <li><a href="{{route('admin.sales.users')}}"
                                        class="{{ (Request::route()->getName() == 'admin.sales.users') ? 'active current-active' : '' }}">Sales
                                        Center Users</a></li>
                                @endif
                                @endif
                                @if(Auth::user()->isAccessLevelToClient())
                                @if(Auth::user()->hasPermissionTo('view-all-agents'))
                                <li><a href="{{route('admin.all.agents')}}"
                                        class="{{ (Request::route()->getName() == 'admin.all.agents') ? 'active current-active' : '' }}">
                                        All Agents</a></li>
                                @endif
                                @else
                                @if(Auth::user()->can('view-all-agents'))
                                <li><a href="{{route('admin.all.agents')}}"
                                        class="{{ (Request::route()->getName() == 'admin.all.agents') ? 'active current-active' : '' }}">
                                        All Agents</a></li>
                                @endif
                                @endif
                                @if(Auth::user()->isAccessLevelToClient())
                                @if(Auth::user()->hasPermissionTo('view-sales-agents'))
                                <li><a href="{{ route('admin.sales.agents') }}"
                                        class="{{ (Request::route()->getName() == 'admin.sales.agents') ? 'active current-active' : '' }}">
                                        Sales Agents</a></li>
                                @endif
                                @else
                                @if(Auth::user()->can('view-sales-agents'))
                                <li><a href="{{ route('admin.sales.agents') }}"
                                        class="{{ (Request::route()->getName() == 'admin.sales.agents') ? 'active current-active' : '' }}">
                                        Sales Agents</a></li>
                                @endif
                                @endif
                                @if(Auth::user()->isAccessLevelToClient())
                                @if(Auth::user()->hasPermissionTo('view-tpv-agents'))
                                <li><a href="{{ route('tpvagents.index') }}"
                                        class="{{ (Request::route()->getName() == 'tpvagents.index') ? 'active current-active' : '' }}">
                                        TPV Agents</a></li>
                                @endif
                                @else
                                @if(Auth::user()->can('view-tpv-agents'))
                                <li><a href="{{ route('tpvagents.index') }}"
                                        class="{{ (Request::route()->getName() == 'tpvagents.index') ? 'active current-active' : '' }}">
                                        TPV Agents</a></li>
                                @endif
                                @endif
                                {{-- @endrole --}}
                            </ul>
                        </li>
                        @endif
                        </ul>
                        </li>
                        @endif
                        </ul>
                    </li>
                        @if(Auth::user()->isAccessLevelToClient())
                        @if(Auth::user()->hasPermissionTo('edit-settings'))
                        <li>
                            <a href="{{route('settings.editWorkspace')}}"
                                class="{{ (Request::route()->getName() == 'settings.editWorkspace' ) ? 'active current-active' : '' }}"><span
                                    class="icon"><img src="{{asset('images/settings.svg')}}" /></span>Config</a>
                        </li>
                        @endif
                        @else
                        @if(Auth::user()->can('edit-settings'))
                        <li>
                            <a href="{{route('settings.editWorkspace')}}"
                                class="{{ (Request::route()->getName() == 'settings.editWorkspace' ) ? 'active current-active' : '' }}"><span
                                    class="icon"><img src="{{asset('images/settings.svg')}}" /></span>Config</a>
                        </li>
                        @endif
                        @endif

                        <!-- <li>
                            <a href="{{route('selfVerificationAllowedZipcode.create')}}" class="{{ (Request::route()->getName() == 'selfVerificationAllowedZipcode.create' ) ? 'active current-active' : '' }}"><span class="icon" ><img
                                            src="{{asset('images/settings.svg')}}"/></span> Verification  Zipcodes</a>
                        </li> -->
                        </ul>
                    </div>

                </div>

                <div class="clearfix"></div>
                <div class="col-xs-8 col-sm-6 col-md-6 left-0">
                    <div id="main">
                        <a class="hamburger header-menu-toggler" href="javascript:void(0)">
                            <div class="patty"></div>
                        </a>

                    </div>
                    <a href="{{ route('dashboard')}}" class="visible-sm mobile-logo visible-xs"><img
                            src="{{asset('images/logo.png')}}" alt="{{ config('app.name', 'Laravel') }}" /></a>
                </div>


                <div class="search-container"></div>
                <div class="col-xs-4 col-sm-6 col-md-6">
                    <?php
                    $firstName  = ucfirst(Auth::user()->first_name[0]);
                    $lastName  = ucfirst(Auth::user()->last_name[0]);
                    ?>
                    <div class="col-xs-12 col-sm-12 col-md-12 login login-info-admin text-right">

                        {{-- @permission('support')
                        <div class="inline-block hidden-xs">
                            <button class="btn btn-green btn-support" onclick="openWidget();"> Support </button>
                        </div>
                        @endpermission --}}
                        @if(Auth::user()->isAccessLevelToClient())
                        @if(Auth::user()->hasPermissionTo('support'))
                            <div class="inline-block hidden-xs">
                                <button class="btn btn-green btn-support" onclick="openWidget();"> Support </button>
                            </div>
                        @endif
                        @else
                        @if(Auth::user()->can('support'))
                            <div class="inline-block hidden-xs">
                                <button class="btn btn-green btn-support" onclick="openWidget();"> Support </button>
                            </div>
                        @endif
                        @endif
                        <div class="loginimg inline-block hidden-xs">
                            @if(Auth::user()->profile_picture !="")
                            <img src="{{ Storage::disk('s3')->url(Auth::user()->profile_picture) }}"
                                class="header-profile-pic" />
                            @else

                            {{--<img src="{{asset('images/login.jpg')}}"/>--}}
                            <div id="profileImage">{{ $firstName .''.$lastName}}</div>
                            @endif

                        </div>
                        <div class="dropdown inline-block">
                            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"> {{ Auth::user()->full_name }}</button>
                            <ul class="dropdown-menu">
                                <li><a href="{{route('edit-profile')}}">Profile</a></li>
                                <li><a href="javascript:void(0)" data-toggle="modal" data-target="#logout_popup">
                                        Log Out
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        @endauth
    </div>

    <!-- end header -->

    <div class="wrapper main-content">

        @yield('content')

        <div class="main-footer">
            © {!! date('Y') !!} <strong>TPV360</strong> | All Rights Reserved
        </div>

    </div>


    <!--logout--popup---start--->

    <div class="modal fade confirmation-model " id="logout_popup">
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
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                class="btn btn-green">Yes</a>
                            <a type="button" class="btn btn-red" data-dismiss="modal">No</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--logout--popup---end--->
    <script type="text/javascript">
        /*** global variables ***/

        const getSalesCenterOptionsUrl = "{{route('getSalesCenterByClientId')}}";
        const getSalesCenterLocationOptionsUrl = "{{route('getSalesCenterLocationOptions')}}";
        const getLocationChannelOptionsUrl = "{{route('getLocationChannels')}}";
    </script>


    <script src="{{ asset('js/datatables/datatables.js')}}"></script>
    <script src="{{ asset('js/datatables/ColReorderWithResize.js')}}"></script>
    <script src="{{ asset('js/datatables/rowsGroup.js')}}"></script>

    <script src="{{ asset('js/datatables/dataTables.hideEmptyColumns.js')}}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/jquery.scrollbar.js') }}"></script>
    <script src="{{ asset('js/gsap/TweenMax.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js') }}"></script>

    <script src="{{ asset('js/dropzone/dropzone.min.js')}}"></script>
    <script src="{{ asset('js/dropzone/scripts.js')}}"></script>

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
    <script src="{{ asset('js/jquery.multiselect.js')}}"></script>


    <script src="{{ asset('js/jquery.nestable.js')}}"></script>
    <script src="{{ asset('js/jquery.gauge.js')}}"></script><!-- dashboard slider -->

    <!-- JavaScripts initializations and stuff -->
    <script src="{{ asset('js/neon-custom.js')}}"></script>


    <!-- Demo Settings -->
    <script src="{{ asset('js/neon-demo.js')}}"></script>
    <!-- Scripts -->

    <script src="{{ asset('js/admin.js') }}"></script>


    <!-- <script src="{{ asset('js/fileuploader/jquery.dm-uploader.js') }}"></script>
    <script src="{{ asset('js/fileuploader/demo-ui.js') }}"></script> -->


    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/parsley.min.js') }}"></script>
    <script>
        window.Parsley.addValidator('maxFileSize', {
            validateString: function (_value, maxSize, parsleyInstance) {
                if (!window.FormData) {
                    alert('You are making all developpers in the world cringe. Upgrade your browser!');
                    return true;
                }
                var files = parsleyInstance.$element[0].files;
                return files.length != 1 || files[0].size <= 5000000;
            },
            requirementType: 'integer',
            messages: {
                en: 'This file should not be larger than %s Mb',
                fr: 'Ce fichier est plus grand que %s mb.'
            }
        });
        setInterval(function () {
            $.post('/logout-tpv-admin', {
                "_token": "{{ csrf_token() }}",
            }, function (data) {
                if (data.logout === true) {
                    location.reload();
                }

            }).fail(function () {
                location.reload();
            });
        }, 10000);


        jQuery(document).ready(function () {
            jQuery('.scrollbar-inner').scrollbar();
        });

        $('body').tooltip({
            selector: '[data-toggle="tooltip"]',
            trigger: "hover"
        });
    </script>

    <!--script for url-show-->
    <script>
        $(document).ready(() => {
            let url = location.href.replace(/\/$/, "");

            if (location.hash) {
                const hash = url.split("#");
                $('#myTab a[href="#' + hash[1] + '"]').tab("show");
                url = location.href.replace(/\/#/, "#");
                history.replaceState(null, null, url);
                setTimeout(() => {
                    $(window).scrollTop(0);
                }, 400);
            }

            $('a[data-toggle="tab"]').on("click", function () {
                let newUrl;
                const hash = $(this).attr("href");

                newUrl = url.split("#")[0] + hash;

                history.replaceState(null, null, newUrl);
            });
        });
    </script>

    <script>
        $(document).ready(function () {

            FreshworksWidget('hide', 'launcher');
            $("#accordian a").click(function () {
                var link = $(this);
                var closest_ul = link.closest("ul");
                var parallel_active_links = closest_ul.find(".active");
                var closest_li = link.closest("li");
                var link_status = closest_li.hasClass("active");
                var count = 0;

                closest_ul.find("ul").slideUp(function () {
                    if (++count == closest_ul.find("ul").length)
                        parallel_active_links.removeClass("active");
                });

                if (!link_status) {
                    closest_li.children("ul").slideDown();
                    closest_li.addClass("active");
                }
            })
        })
    </script>

    @stack('scripts')
</body>

</html>