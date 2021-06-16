<!DOCTYPE html>
<html  ng-app="fbApp">
 <?php
    $company = App\Model\helpdesk\Settings\Company::where('id', '=', '1')->first();
    $portal = App\Model\helpdesk\Theme\Portal::where('id', '=', 1)->first();
    $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->value('name');
    $title_name = isset($title) ? $title : "SUPPORT CENTER";

 if ($portal->admin_header_color == "skin-red") {
            $selectedColor = '#dd4b39';
        }
        if ($portal->admin_header_color == "skin-yellow") {
            $selectedColor = '#f39c12';
        }
        if ($portal->admin_header_color == "skin-blue") {
            $selectedColor = '#3c8dbc';
        }
        if ($portal->admin_header_color == "skin-black") {
            $selectedColor = '#222d32';
        }
        if ($portal->admin_header_color == "skin-green") {
            $selectedColor = '#00a65a';
        }
        if ($portal->admin_header_color == "skin-purple") {
            $selectedColor = '#605ca8';
        }
        if ($portal->admin_header_color == "null") {
            $selectedColor = '#FFFFFF';
        }

 ?>
 <style type="text/css">
     body{
        padding-right: 0 !important;
    }

    #chumper{
      display: table-cell!important;
    }
 </style>
    <head>
        <meta charset="UTF-8">
           @yield('meta-tags')

         <title> @yield('title') {!! strip_tags($title_name) !!} </title>
        <meta name="api-base-url" content="{{ url('/') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta name="api-base-url" content="{{ url('/') }}" />
        <!-- faveo favicon -->
    
        <link href="{{$portal->icon}}" rel="shortcut icon">
       
        <!-- Bootstrap 3.4.1 -->
        <link rel="stylesheet" href="{{assetLink('css','bootstrap-latest')}}"><!-- Font Awesome Icons -->
        <link href="{{assetLink('css','font-awesome')}}" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="{{assetLink('css','ionicons')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
        <!-- Theme style -->
        <link href="{{assetLink('css','AdminLTE')}}" rel="stylesheet" type="text/css" id="adminLTR" media="none" onload="this.media='all';"/>

        <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
        <link href="{{assetLink('css','all-skins')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';" />

        <link href="{{assetLink('css','tabby')}}" rel="stylesheet" type="text/css" />

        <link  href="{{assetLink('css','admin-common')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">


        <link  href="{{assetLink('css','editor')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
        <script src="{{assetLink('js','jquery-3')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','ckeditor')}}"></script>
        <script src="{{assetLink('js','polyfill')}}"></script>
        <style type="text/css">
            .navbar-collapse{
        background-color: {{$selectedColor}}!important;
    }
    .logo{
        background-color: {{$selectedColor}}!important;
    }
    .skin-black .main-header .navbar .nav>li>a {
    color: #eee;
   }
.skin-black .main-header .navbar>.sidebar-toggle {
    color: #fff;
   }
        </style>

        <?php
            use App\Model\helpdesk\Settings\Alert;
            $browser_status = false;
            $alert =  new Alert;
            $enabled = $alert->where('key', 'browser_notification_status')->value('value');
            $app_id = $alert->where('key', 'api_id')->value('value');

            $in_app_notification = $alert->where('key', 'browser-notification-status-inbuilt')->value('value');
            $in_app_notification_status = false;
            if($enabled == 1)
                $browser_status = 0;
            if($in_app_notification == 1)
                $in_app_notification_status = 1;
        ?>

         @if($enabled == 1)
         <link rel="manifest" href="/manifest.json">
        <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" ></script>
        <script type="text/javascript">
            var OneSignal = window.OneSignal || [];
            OneSignal.push(["init", {
                appId: "<?php echo $app_id; ?>",
                autoRegister: false,
                notifyButton: {
                    enable: "{{$browser_status}}" /* Set to false to hide */
                }
            }]);
            var user =  "{{Auth::guest()}}";
            if(!user){
                var user_id = "<?php if(Auth::user()){ echo Auth::user()->hash_ids;} ?>";
                var user_role = "<?php if(Auth::user()){ echo Auth::user()->role;} ?>";
                var user_name = "<?php if(Auth::user()){ echo Auth::user()->user_name;} ?>";
                OneSignal.push(function() {
                    //These examples are all valid
                    OneSignal.sendTag("user_name",user_name);
                    OneSignal.sendTag("user_id", user_id);
                    OneSignal.sendTag("user_role", user_role);
                });
            }

        </script>
        @endif
        <script type="text/javascript" src="{{asset('browser-detect.min.js')}}"></script>

        @yield('HeadInclude')
        <!-- rtl brecket -->
<!--  <style type="text/css">
     *:after {
    content: "\200E‎";
}
 </style> -->
       <style>

@if(isPlugin('ServiceDesk'))
     .content-heading-anchor{
          margin-top: -44px;
     }
@endif
@if(Lang::getLocale() == "ar")
.datepicker {
   direction: rtl;
}
.datepicker.dropdown-menu {
   right: initial;
}
@endif


               input[type=number]::-webkit-inner-spin-button,
               input[type=number]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                margin: 0;
               }
            </style>
@yield('custom-css')



  </head>
    @if($portal->admin_header_color)
    <body class="{{$portal->admin_header_color}} fixed"  style="display:none" ng-controller="MainCtrl">

    @else
    <body class="skin-yellow fixed" ng-controller="MainCtrl">

    @endif

        <div class="wrapper">
            <header class="main-header">

               <a href="{{$company->website}}" class="logo logocolor">
                  <img src='{{$portal->logo}}' class="img-rounded" alt="Cinque Terre"  height="45">
                </a>
            
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <?php $notifications = App\Http\Controllers\Common\NotificationController::getNotifications(); ?>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="navbar-collapse">

                        <ul class="nav navbar-nav navbar-left">
                            <li @yield('settings') ><a href="{!! url('dashboard') !!}">{!! Lang::get('lang.agent_panel') !!}</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right" id="right-menu">
                            <li><a href="{{url('admin')}}" onclick="removeurl()">{!! Lang::get('lang.admin_panel') !!}</a></li>
                            <!-- START NOTIFICATION -->
                            @include('themes.default1.inapp-notification.notification')

                            <!-- END NOTIFICATION -->
                        <li class="dropdown">
                            <?php $src = Lang::getLocale().'.png'; ?>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><img src="{{assetLink('image','flag').'/'.$src}}"></img> &nbsp;<span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                @foreach($langs as $key => $value)
                                            <?php
                                             $src = $key.".png";
                                             $checkCdnStatus = \App\Model\helpdesk\Settings\CommonSettings::where('option_name', 'cdn_settings')->value('option_value');

                                            ?>
                                             <li><a href="#" id="{{$key}}" onclick="changeLang(this.id)"><img src="{{assetLink('image','flag').'/'.$src}}"></img>&nbsp;{{$value[0]}}&nbsp;
                                            @if(Lang::getLocale() == "ar")
                                            &rlm;
                                            @endif
                                            ({{$value[1]}})</a></li>
                                @endforeach
                            </ul>
                        </li>
                        <?php
                           $onerrorImage = assetLink('image','contacthead');
                            ?>
                       
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                @if(Auth::user())

                                <img src="{{Auth::user()->profile_pic}}" onerror="this.src='{{$onerrorImage}}'" class="user-image" alt="User Image"/>

                                <span class="hidden-xs" title="{{Auth::user()->fullname}}">{{(ucfirst(str_limit(Auth::user()->fullname, 15)))}}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header" style="background-color:#343F44;">
                                    @if(Auth::user())
                                    <img src="{{Auth::user()->profile_pic}}" onerror="this.src='{{$onerrorImage}}'" class="img-circle" alt="User Image" />

                                    <p>
                                    <span>{!! Lang::get('lang.hello') !!}</span><br/>
                                    <span title="{{Auth::user()->fullname}}">{{(ucfirst(str_limit(Auth::user()->fullname, 15)))}}<br/>{{Auth::user()->role}}</span>

                                    </p>
                                    <br/>
                                    @endif
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer"  style="background-color:#1a2226;">
                                    <div class="pull-left">
                                        <a href="{{url('profile')}}" class="btn btn-info btn-sm"><b>{!! Lang::get('lang.profile') !!}</b></a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="#" class="btn btn-danger btn-sm" id="logout"><b>{!! Lang::get('lang.sign_out') !!}</b></a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        </ul>
                    </div>
                </nav>
            </header>

            <!-- Left side column. contains the logo and sidebar -->
            <div id="navigation-container">
                <admin-navigation-bar></admin-navigation-bar>
            </div>

            <!-- Right side column. Contains the navbar and content of the page -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header" >
                    <!--<div class="row">-->
                    <!--<div class="col-md-6">-->
                    @yield('PageHeader')
                    <!--</div>-->
                    @if(Breadcrumbs::exists())
                    {!! Breadcrumbs::render() !!}
                    @endif
                    <!--</div>-->
                </section>

                <!-- Main content -->
                <section class="content">
                    @if($dummy_installation == 1 || $dummy_installation == '1')
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="icon fa  fa-exclamation-triangle"></i> {{Lang::get('lang.dummy_data_installation_message')}} <a href="{{route('clean-database')}}">{{Lang::get('lang.click')}}</a> {{Lang::get('lang.clear-dummy-data')}}
                    </div>
                    @else
                        @if (!$is_mail_conigured)
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                @if (\Auth::user()->role == 'admin')
                                    {{Lang::get('lang.system-outgoing-incoming-mail-not-configured')}}&nbsp;<a href="{{URL::route('emails.create')}}">{{Lang::get('lang.confihure-the-mail-now')}}</a>
                                @else
                                    {{Lang::get('lang.system-mail-not-configured-agent-message')}}
                                @endif
                            </div>
                        @endif
                    @endif

                    {{--  this script can be used to store data which is common among all pages  --}}
                    <script type="text/javascript">
                        @php
                            $user = Auth::user();
                        @endphp
                        sessionStorage.setItem('full_name', '{{ $user->full_name }}');
                        sessionStorage.setItem('profile_pic', '{!! $user->profile_pic !!}');
                        sessionStorage.setItem('user_id', '{{ $user->id }}');
                        sessionStorage.setItem('is_rtl', '{{ App::getLocale() == "ar" ? 1 : 0}}');
                        sessionStorage.setItem('header_color', '{{ $selectedColor }}');
                        sessionStorage.setItem('data_time_format', '{{ dateTimeFormat() }}');
                        sessionStorage.setItem('date_format', '{{ dateFormat() }}');
                        sessionStorage.setItem('user_role', '{{ $user->role }}');
                    </script>
                    <script src="{{bundleLink('js/lang')}}" type="text/javascript"></script>
                    <script src="{{bundleLink('js/common.js')}}" type="text/javascript"></script>
                    <script src="{{bundleLink('js/navigation.js')}}" type="text/javascript"></script>
                    <div class="custom-div-top" id="custom-div-top"></div>
                    @yield('content')
                    <div class="custom-div-bottom" id="custom-div-bottom"></div>
                </section><!-- /.content -->
                <!-- /.content-wrapper -->
            </div>



            <footer class="main-footer">


                  @if(!\Event::dispatch('helpdesk.apply.whitelabel'))
                 <div style="position: fixed;right:0;bottom:0">
                    <button data-toggle="control-sidebar" onclick="openSlide()" style="margin-right:20px"  class="btn btn-primary helpsection">
                        {!! Lang::get('lang.have_a_question') !!}
                   &nbsp;&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></button>
                </div>
             @endif

                <div class="hidden-xs pull-right ">

                   @if(\Event::dispatch('helpdesk.apply.whitelabel'))
                    <b>Version</b> {!! Config::get('app.tags') !!}

                    @else
                    <b>Version</b> {!! Config::get('app.version') !!}

                    @endif



                </div>
                <?php
                $company = App\Model\helpdesk\Settings\Company::where('id', '=', '1')->first();
                ?>
                <strong>{!! Lang::get('lang.copyright') !!} &copy; {!! date('Y') !!}  <a href="{!! $company->website !!}" target="_blank">{!! $company->company_name !!}</a>.</strong>

               @if(\Event::dispatch('helpdesk.apply.whitelabel'))
                   {!! Lang::get('lang.all_rights_reserved') !!}
                @else
                 {!! Lang::get('lang.all_rights_reserved') !!}. {!! Lang::get('lang.powered_by') !!} <a href="https://www.faveohelpdesk.com/" target="_blank">Faveo</a>

                @endif



                 <!-- {!! Lang::get('lang.powered_by') !!} <a href="http://www.faveohelpdesk.com/" target="_blank">Faveo</a> -->
            </footer>
             <aside class="right-side-menu control-sidebar-dark" id="right"  style="padding-top:0;bottom: 348px;background: transparent">
                <!-- Create the tabs -->
                <ul class="nav nav-tabs nav-justified control-sidebar-tabs helptopi"  style="background: transparent">
                    <li class="remov"><a href="javascript:void(0)" onclick="openSlide()" class="help-widget-close" style="background-color: black;height: 41px"><i class="fa fa-times" aria-hidden="true"></i></a></li>
                    <li ><a href="#settings-tab" data-toggle="tab" id="trapezoid" style="background-color:transparent;color:#444;" ><i class="fa fa-paper-plane-o" aria-hidden="true" ></i> &nbsp{!! Lang::get('lang.mail_us') !!}
                        </a><span class="tab-slant"></span></li>
                    <li class="active" ><a href="#home-tab" data-toggle="tab" id="trapezoid" style="background-color: transparent;color:#444;"><i class="fa fa-question-circle" aria-hidden="true"></i> {!! Lang::get('lang.help') !!}</a><span class="tab-slant"></span></li>
                </ul>
            </aside>
            <aside class="right-side-menu1 control-sidebar-dark" id="right1"  style="padding-top:0;height: 348px;background-color: #f0f0f0;border: 1px solid gainsboro;width">
                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Home tab content -->
                    <div class="tab-pane active row" id="home-tab">
                        <div class="col-sm-3" style="margin-top:67px">
                            <div style="text-align:center"><h3 class="space-on-bottom-lg" style="color:black"> {!! Lang::get('lang.prefer_email_instead?') !!} </h3>
                                <div><button class="btn btn-default space-on-bottom-20px space-on-top-10px" onclick="mailTab()">
                                        <span class="icon icon-envelope icon-gray tail5px"></span> {!! Lang::get('lang.write_to_us') !!}</button>
                                    <p style="margin-top:7px;color:black">{!! Lang::get('lang.we_are_super_quick_in_responding_to_your_queries.') !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9" style="border-left:1px solid gainsboro;height:346px">
                            <ul class="control-sidebar-menu">
                                <div class="col-sm-12" style="padding-right: 50px;margin-top: 20px;margin-bottom: 20px">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-11">
                                        <div class="search-box hide"><input type="text" data-autofocus="true" placeholder="What can we help you with? Start typing your question..."></div>
                                    </div>
                                </div>
                                <div class="col-sm-12" style="padding-left: 10%;padding-right: 3px;margin-top: 20px;margin-bottom: 20px">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12" style="padding-left:20px">
                                                 <a href="https://support.faveohelpdesk.com/show/the-staff-panel-agents" target="_blank">
                                                    <i class="menu-icon fa fa-user bg-light-blue"></i>
                                                       </a>
                                                </div>
                                                <div class="menu-info col-sm-12" style="padding-left:20px">
                                                    <h4 class="control-sidebar-subheading" style="line-height: 2.5;margin-left: 5px;">

                                                        <a href="https://support.faveohelpdesk.com/show/the-staff-panel-agents" target="_blank">
                                                            {!! Lang::get('lang.staffs') !!}


                                                        </a>
                                                    </h4>

                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12">
                                                <a href="https://support.faveohelpdesk.com/category-list/working-with-emails" target="_blank">
                                                    <i class="menu-icon fa fa-envelope bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">

                                                    <h4 class="control-sidebar-subheading" style="line-height: 2.5">
                                                        <a href="https://support.faveohelpdesk.com/support/category-list/working-with-emails" target="_blank">
                                                            {!! Lang::get('lang.email') !!}
                                                        </a>
                                                    </h4>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12" style="padding-left: 20px;">
                                                 <a href="https://support.faveohelpdesk.com/category-list/managing-your-system" target="_blank">
                                                    <i class="menu-icon fa fa-database bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                    <h4 class="control-sidebar-subheading" style="line-height: 2.5">
                                                        <a href="https://support.faveohelpdesk.com/support/category-list/managing-your-system" target="_blank">
                                                            {!! Lang::get('lang.manage') !!}
                                                        </a>
                                                    </h4>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12">
                                                 <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    <i class="menu-icon fa fa-ticket bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                    <h4 class="control-sidebar-subheading" style="line-height: 2.5">
                                                        <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                            {!! Lang::get('lang.ticket') !!}
                                                        </a>
                                                    </h4>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12" style="padding-left: 20px;">
                                                 <a href=" https://support.faveohelpdesk.com/search?s=settings" target="_blank">

                                                    <i class="menu-icon fa fa-cog bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">

                                                    <h4 class="control-sidebar-subheading" style="line-height: 2.5">
                                                    <a href="https://support.faveohelpdesk.com/search?s=settings" target="_blank">{!! Lang::get('lang.settings') !!}</h4>   </a>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
                                <div class="col-sm-12" style="padding-left: 10%;padding-right: 3px;margin-top: 20px;margin-bottom: 20px">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12" style="padding-left:20px">
                                                <a href="https://support.faveohelpdesk.com/show/500-error" target="_blank">
                                                    <i class="menu-icon fa fa-bug bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                    <h4 class="control-sidebar-subheading" style="padding-top: 10px">
                                                    <a href="https://support.faveohelpdesk.com/show/500-error" target="_blank">

                                                    {!! Lang::get('lang.error-debug') !!}
                                                    </a>
                                                    </h4>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12" style="padding-left:20px">
                                                <a href="https://support.faveohelpdesk.com/show/widgets" target="_blank">
                                                    <i class="menu-icon fa fa-pie-chart bg-light-blue"></i></a>
                                                </div>
                                                <div class="menu-info col-sm-12">

                                                    <h4 class="control-sidebar-subheading" style="line-height: 2.5"> <a href="https://support.faveohelpdesk.com/show/widgets" target="_blank">{!! Lang::get('lang.widgets') !!}</a></h4>              </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12">
                                                 <a href="https://support.faveohelpdesk.com/category-list/plugin-and-packages" target="_blank">
                                                    <i class="menu-icon fa fa-plug bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                    <h4 class="control-sidebar-subheading" style="line-height: 2.5">
                                                    <a href="https://support.faveohelpdesk.com/category-list/plugin-and-packages" target="_blank">

                                                    {!! Lang::get('lang.plugin') !!}
                                                    </a>
                                                    </h4>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12">
                                                <a href="https://support.faveohelpdesk.com/category-list/api" target="_blank">
                                                    <i class="menu-icon fa fa-cogs bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                    <h4 class="control-sidebar-subheading" style="line-height: 2.5;margin-left: 10px;"><a href="https://support.faveohelpdesk.com/category-list/api" target="_blank">{!! Lang::get('lang.api') !!}</a></h4>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12">
                                                <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    <i class="menu-icon fa fa-lock bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                    <h4 class="control-sidebar-subheading" style="line-height: 2.5">
                                                    <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    {!! Lang::get('lang.logs') !!}
                                                    </a>
                                                    </h4>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
                            </ul>
                        </div>
                        <!-- /.control-sidebar-menu -->

                    </div>
                    <!-- /.tab-pane -->
                    <!-- Settings tab content -->
                    <div class="tab-pane row" id="settings-tab">
                        <div class="col-sm-6" style="padding:7%;text-align:center">
                            <button class="btn btn-default" onclick="helpTab()"><i class="glyphicon glyphicon-search"></i>&nbsp;{!! Lang::get('lang.browse_help_articles') !!}</button>
                        </div>




                        <div class="col-sm-6" style="border-left:1px solid gainsboro;height:346px;padding-top: 45px;" ng-controller="helptopicCtrl">
                            <form method="post" style="margin-top: 25px" id="helpForms">
                                <div class="alert help-success alert-dismissable pull-center" style="display: none;background-color:green;color:white;">
                                    <i class="fa  fa-check-circle"></i>
                                    <span class="success-msg" ></span>
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>


                                </div>
                                <div class="alert help-danger alert-dismissable pull-center" style="display: none;background-color:red;color:white;">
                                    <i class="fa  fa-check-circle"></i>
                                    <span class="error-msg" ></span>
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>


                                </div>
                                <div class="col-sm-12" style="padding-left: 2px;padding-right: 5px">
                                    <div class="col-sm-2">
                                    </div>
                                    <div class="col-sm-2" style="padding-left: 0px;padding-right: 0px">
                                        <label style="line-height: 2.0;color: black">{!! Lang::get('lang.email') !!}</label>
                                    </div>
                                    <div class="col-sm-5" style="padding-left: 0px;padding-right: 0px">
                                        <input type="eamil" name="help_email" id="help_email" value="support@ladybirdweb.com" class="form-control" placeholder="Enter Your Email" style="border-radius: 0px;" disabled="disabled">
                                    </div>
                                </div>


                                <!-- support@ladybirdweb.com -->
                                <div class="col-sm-12" style="margin-top: 20px;padding-left: 2px;padding-right: 5px">
                                    <div class="col-sm-2">
                                    </div>
                                    <div class="col-sm-2" style="padding-left: 0px;padding-right: 0px">
                                        <label style="line-height: 2.0;color: black">{!! Lang::get('lang.subject') !!}</label>
                                    </div>
                                    <div class="col-sm-5" style="padding-left: 0px;padding-right: 0px">
                                        <input type="text" name="help_subject" id="help_subject" class="form-control" placeholder="Subject" style="border-radius: 0px;" required="required">
                                    </div>
                                </div>
                                <div class="col-sm-12" style="margin-top: 20px;padding-left: 2px;padding-right: 5px">
                                    <div class="col-sm-2">
                                    </div>
                                    <div class="col-sm-2" style="padding-left: 0px;padding-right: 0px">
                                        <label style="line-height: 2.0;color: black">{!! Lang::get('lang.message') !!}</label>
                                    </div>
                                    <div class="col-sm-5" style="padding-left: 0px;padding-right: 0px">
                                        <textarea name="help_massage"  id="help_massage" class="form-control" style="border-radius: 0px;" placeholder="Message Here" required="required"></textarea>
                                    </div>

                                </div>
                                <div class="col-sm-12" style="text-align: center;margin-top: 20px;">
                                    <div class="col-sm-4">
                                    </div>
                                    <div class="col-sm-3">
                                        <div style="float:left"><div class="fileupload-wrapper" style="position: relative; overflow: hidden; direction: ltr;"><a href="#" tabindex="-1" class="file-attach-link" style="color: #00c0ef;"><i class="glyphicon glyphicon-paperclip small"></i>{!! Lang::get('lang.attachment') !!}</a>
                                                <input qq-button-id="9f785c98-e24f-476a-ad1d-790faf404528" enctype ='multipart/form-data' multiple="true" type="file" name="help_doc" id="helpdocs" title="File input" files='true' style="position: absolute; right: 0px; top: 0px; font-family: Arial; font-size: 118px; margin: 0px; padding: 0px; cursor: pointer; opacity: 0; height: 100%;"></div>
                                                <br>
                                              <div class='uploadFiles'></div>
                                            </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="btn-group" id="help_form">
                                            <button class="btn btn-primary" id="myButton1" ng-click="helpForm()" disabled="disabled">{!! Lang::get('lang.sent') !!}</button>


                                            <div class="load"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>


                        <!-- /.tab-pane -->
                    </div>
                    <script type="text/javascript">
                                    function scroll2down(e){
                                       var go=$(e).offset().top;
                                       //alert(go);
                                         $('.sidebar').animate({scrollTop:go+"px"}, 500);
                                    }

                                        $('#helpForms').on('input', function(){

                                var subject = $('#help_subject').val();
                                        var massage = $('#help_massage').val();
                                        if (subject != "" && massage != ""){
                                $('#myButton1').removeAttr('disabled');
                                }
                                else{
                                $('#myButton1').attr('disabled', 'disabled');
                                }

                                });

                     </script>

            </aside>
        </div><!-- ./wrapper -->
        <script  type="text/javascript">
            localStorage.setItem('PATH', '{{asset("/")}}');
            localStorage.setItem('CSRF', '{{ csrf_token() }}');
            localStorage.setItem('NOTI_COND', '{{$in_app_notification}}');
            localStorage.setItem('GUEST', '{{Auth::guest()}}');
            localStorage.setItem('LANGUAGE', '{{Lang::getLocale()}}');
            localStorage.setItem('PLUGIN', '{{isPlugin()}}');
        </script>
        <!-- Bootstrap 3.3.2 JS -->
        <script src="{{assetLink('js','bootstrap-latest')}}" type="text/javascript"></script>

        <!-- AdminLTE App -->
        <script src="{{assetLink('js','app-min')}}" type="text/javascript"></script>
        <!-- iCheck -->

        <!-- select2 -->
        <script src="{{assetLink('js','nprogress')}}" type="text/javascript"></script>



@if(isPlugin('ServiceDesk'))
<script>
    $(function(){
        $('.content-heading-anchor').next().removeClass('content');
    })
</script>
@endif
@if (trim($__env->yieldContent('no-toolbar')))
    <h1>@yield('no-toolbar')</h1>
@else

@endif
<script type="text/javascript">
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
    });
    $('#Form').submit(function (e) {
        if ($('#mobile').parent().hasClass('has-error')) {
            var alert_msg = "{{Lang::get('lang.please-check-mobile-number')}}";
            alert(alert_msg);
            e.preventDefault();
        } else {
            var $this = $('#submit');
            $this.button('loading');
            $('#Edit').modal('show');
        }

    });

    // logout click function
    $("#logout").click(function(){
    $.ajax({
        /* the route pointing to the post function */
        url: '{{url("auth/logout")}}',
        type: 'POST',
        data: { "_token": "{{ csrf_token() }}"},
        /* remind that 'data' is the response of the AjaxController */
        success: function (response) { 
             window.location.href = response.data;
             }
        }); 
    });
</script>
<script src="{{bundleLink('js/admin.js')}}" type="text/javascript"></script>
<script src="{{assetLink('js','angular')}}" type="text/javascript"></script>
<script src="{{assetLink('js','angular-moment')}}" type="text/javascript"></script>
<script src="{{assetLink('js','bsSwitch')}}" type="text/javascript"></script>
<script src="{{assetLink('js','angular-desktop-notification')}}" type="text/javascript"></script>

<script src="{{assetLink('js','ui-bootstrap-tpls')}}"></script>
<script src="{{assetLink('js','main')}}"></script>
<script src="{{assetLink('js','handleCtrl')}}"></script>
<script src="{{assetLink('js','nodeCtrl')}}"></script>
<script src="{{assetLink('js','nodesCtrl')}}"></script>

<script src="{{assetLink('js','treeCtrl')}}"></script>
<script src="{{assetLink('js','uiTree')}}"></script>
<script src="{{assetLink('js','uiTreeHandle')}}"></script>
<script src="{{assetLink('js','uiTreeNode')}}"></script>

<script src="{{assetLink('js','uiTreeNodes')}}"></script>
<script src="{{assetLink('js','helper')}}"></script>
<script src="{{assetLink('js','ng-flow-standalone')}}" ></script>
<script src="{{assetLink('js','fusty-flow')}}" ></script>

<script src="{{assetLink('js','fusty-flow-factory')}}" ></script>
<script src="{{assetLink('js','ng-file-upload')}}"></script>
<script src="{{assetLink('js','ng-file-upload-shim')}}"></script>
<script src="{{assetLink('js','tw-currency-select')}}"></script>
<script src="{{assetLink('js','angular-admin-script')}}" type="text/javascript"></script>
<?php \Event::dispatch('timeline-customjs', [['fired_at' => 'adminlayout','request' => Request()]]); ?>
<?php \Event::dispatch('admin-panel-scripts-dispatch') ?>
@yield('FooterInclude')
@stack('scripts')
</body>
</html>
