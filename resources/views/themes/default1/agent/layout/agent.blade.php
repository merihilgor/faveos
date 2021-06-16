<!DOCTYPE html>
<html ng-app="fbApp">
    <?php
    $segment = "";
    $company = App\Model\helpdesk\Settings\Company::where('id', '=', '1')->first();
    $portal = App\Model\helpdesk\Theme\Portal::where('id', '=', 1)->first();
    $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->value('name');
    $title_name = isset($title) ? $title : "SUPPORT CENTER";
    ?>
    <head>
        <meta charset="UTF-8">
         @yield('meta-tags')
         <title> {!! strip_tags($title_name) !!} </title>

        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta name="_token" content="{!! csrf_token() !!}"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="api-base-url" content="{{ url('/') }}" />
        <!-- faveo favicon -->
    
        <link href="{{$portal->icon}}" rel="shortcut icon">
        <!-- Bootstrap 3.4.1 -->

        <link rel="stylesheet" href="{{assetLink('css','bootstrap-latest')}}"><!-- Font Awesome Icons -->
        <link href="{{assetLink('css','font-awesome')}}" rel="stylesheet" type="text/css" />
        <link href="{{assetLink('css','tabby')}}" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="{{assetLink('css','ionicons')}}" rel="stylesheet"  type="text/css" />
        <!-- Theme style -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <link href="{{assetLink('css','editor')}}" rel="stylesheet" type="text/css"/>
        <link href="{{assetLink('css','AdminLTE')}}" rel="stylesheet" type="text/css" id="adminLTR"/>
         <link href="{{assetLink('css','jquery-rating')}}" rel="stylesheet" type="text/css" />
        

       <!--  <link href="{{asset("lb-faveo/rtl/css/AdminLTE.css")}}" id="adminRTL" rel="stylesheet" type="text/css" disabled='disabled' /> -->

        <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
        <link href="{{assetLink('css','all-skins')}}" rel="stylesheet"  type="text/css" />

         <link href="{{assetLink('css','daterangepicker')}}"  rel="stylesheet" type="text/css" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
         <link href="{{assetLink('css','new-common')}}" rel="stylesheet" type="text/css"/>
        <link href="{{assetLink('css','nprogress')}}" rel="stylesheet" type="text/css"/>
        <link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>
        <link href="{{assetLink('css','ckeditor-css')}}" rel="stylesheet" type="text/css" />
        <!--calendar -->
              <?php
        if ($portal->agent_header_color == "skin-red") {
            $selectedColor = '#dd4b39';
        }
        if ($portal->agent_header_color == "skin-yellow") {
            $selectedColor = '#f39c12';
        }
        if ($portal->agent_header_color == "skin-blue") {
            $selectedColor = '#3c8dbc';
        }
        if ($portal->agent_header_color == "skin-black") {
            $selectedColor = '#222d32';
        }
        if ($portal->agent_header_color == "skin-green") {
            $selectedColor = '#00a65a';
        }
        if ($portal->agent_header_color == "skin-purple") {
            $selectedColor = '#605ca8';
        }
        if ($portal->agent_header_color == "null") {
            $selectedColor = '#FFFFFF';
        }
        ?>


        <!-- Browser Notification -->
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
        <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js"></script>
        <script type="text/javascript">
            // in app notification

            // in app notification ends
            var user =  "{{Auth::guest()}}";
            var OneSignal = window.OneSignal || [];
            OneSignal.push(["init", {
                appId: "<?php echo $app_id; ?>",
                autoRegister: false,
                notifyButton: {
                    enable: "{{$browser_status}}" /* Set to false to hide */
                }
            }]);

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
        <script type="text/javascript" src="{{asset('browser-detect.min.js')}}" async></script>
        <script src="{{assetLink('js','jquery-3')}}" type="text/javascript" media="none" onload="this.media='all';"></script>
        <script src="{{assetLink('js','polyfill')}}"></script>
        <script src="{{assetLink('js','select2')}}" type="text/javascript"></script>
        
        @yield('HeadInclude')
        <!-- rtl brecket -->
<style type="text/css">

    @-moz-document url-prefix() {
      .content-wrapper {
        min-height: 570px !important;
      }
    }
#chumper{
      display: table-cell!important;
    }

.skin-black .main-header .navbar .nav>li>a {
    color: #eee;
}
body{
    padding-right: 0 !important;
}
.skin-black .main-header .navbar>.sidebar-toggle {
    color: #fff;
}
.navbar-collapse{
        background-color: {{$selectedColor}}!important;
    }
    .logocolor{
        background-color: {{$selectedColor}}!important;
    }
.tabs-horizontal > .active, .tabs-stacked > .active {
        background-color: {{$selectedColor}}!important;
    }
    .tab-content1  {
        background-color: {{$selectedColor}}!important;
    }
    #bar a:focus, #bar a:hover{
        background-color: {{$selectedColor}}!important;
    }

            .loading{
                background-image : url('{{assetLink('image','loading')}}');
                background-repeat:no-repeat;
            }
            .loading:after {
                content: "{!! Lang::get('lang.sending') !!}";
                text-align : right;
                padding-left : 25px;
            }

              .modal-open {
    overflow: auto!important;
}

@if(Lang::getLocale() == "ar")
.datepicker {
   direction: rtl;
}
.datepicker.dropdown-menu {
   right: initial;
}
@endif
select.form-control{
    line-height: 2;
}
        </style>
            <style>
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
    @if($portal->agent_header_color)
    <body class="{{$portal -> agent_header_color}} fixed" style="display:none">
     <!-- <div class="loader1" style="z-index:2000"></div> -->
        @else
    <body class="skin-yellow fixed" style="display:none">
     <!-- <div class="loader1" style="z-index:2000"></div> -->
        @endif
        <div class="wrapper" >
            <header class="main-header">
               <a href="{{$company->website}}" class="logo logocolor">
                  <img src='{{$portal->logo}}' class="img-rounded" alt="Cinque Terre"  height="45">
                </a>

                @php
                    $onerrorImage = assetLink('image','contacthead');
                @endphp

                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <div class="collapse navbar-collapse">
                        <ul class="nav navbar-nav navbar-right" id="right-menu">
                            @if($auth_user_role == 'admin')
                            <li><a href="{{url('admin')}}">{!! Lang::get('lang.admin_panel') !!}</a></li>

                            @endif
                            <!-- This is a temp event must be removed with dynamic navigation-->
                            <?php \Event::dispatch('update_topbar_right', []); ?>
                            @include('themes.default1.update.notification')
                            <!-- START NOTIFICATION -->
                            @include('themes.default1.inapp-notification.notification')

                            <!-- END NOTIFICATION -->
                            <li class="dropdown">
                                <?php $src = Lang::getLocale().'.png'; ?>
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><img src="{{assetLink('image','flag').'/'.$src}}" ></img> &nbsp;<span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    @foreach($langs as $key => $value)
                                            <?php
                                             $src = $key.".png";
                                            ?>
                                            <li><a href="#" id="{{$key}}" onclick="changeLang(this.id)"><img src="{{assetLink('image','flag').'/'.$src}}"></img>&nbsp;{{$value[0]}}&nbsp;
                                            @if(Lang::getLocale() == "ar")
                                            &rlm;
                                            @endif
                                            ({{$value[1]}})</a></li>
                                    @endforeach
                                </ul>
                            </li>

                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    @if($auth_user_id)
                                    <img src="{{$auth_user_profile_pic}}" onerror="this.src='{{$onerrorImage}}'" class="user-image" alt="User Image"/>
                                   <span class="hidden-xs" title="{{Auth::user()->fullname}}">{{(ucfirst(str_limit(Auth::user()->fullname, 15)))}}</span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header"  style="background-color:#343F44;">
                                        <img src="{{$auth_user_profile_pic}}" onerror="this.src='{{$onerrorImage}}'"class="img-circle" alt="User Image" />
                                        <p>
                                         <span>{!! Lang::get('lang.hello') !!}</span><br/>
                                    <span title="{{Auth::user()->fullname}}">{{(ucfirst(str_limit(Auth::user()->fullname, 15)))}}<br/></span>
                                    <span>{{Auth::user()->role}}</span>
                                        </p>
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer" style="background-color:#1a2226;">
                                        <div class="pull-left">
                                            <a href="{{URL::route('profile')}}" class="btn btn-info btn-sm"><b>{!! Lang::get('lang.profile') !!}</b></a>
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

            @include('themes.default1.common.socket')


            <!-- Left side column. contains the logo and sidebar -->
            <!-- Left side column. contains the logo and sidebar -->
            <div id="navigation-container">
                <agent-navigation-bar></agent-navigation-bar>
            </div>

            <?php
                $segments = \Request::segments();
                $segment = "";
                foreach($segments as $seg){
                    $segment.="/".$seg;
                }
            ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <!-- do nothing-->
                <section class="content-header">
                   @yield('PageHeader')
                    @if(Breadcrumbs::exists())
                    {!! Breadcrumbs::render() !!}
                    @endif
                </section>
                <!-- Main content -->
                <section class="content">
                    @if($dummy_installation == 1)
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="icon fa  fa-exclamation-triangle"></i> @if (\Auth::user()->role == 'admin')
                            {{Lang::get('lang.dummy_data_installation_message')}} <a href="{{route('clean-database')}}">{{Lang::get('lang.click')}}</a> {{Lang::get('lang.clear-dummy-data')}}
                        @else
                            {{Lang::get('lang.clear-dummy-data-agent-message')}}
                        @endif
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
                        sessionStorage.setItem('timezone', '{{ agentTimeZone() }}');
                        sessionStorage.setItem('user_role', '{{ $user->role }}');
                    </script>
                    <script src="{{bundleLink('js/lang')}}" type="text/javascript"></script>
                    <script src="{{bundleLink('js/common.js')}}" type="text/javascript"></script>
                    <script src="{{bundleLink('js/navigation.js')}}" type="text/javascript"></script>
                    <div class="custom-div-top" id="custom-div-top"></div>
                    @yield('content')
                    <?php
                      // plugins can add inject scripts or bundles by listening to this event in agent panel.
                      Event::dispatch('agent-panel-scripts-dispatch');
                    ?>
                    <div class="custom-div-bottom" id="custom-div-bottom"></div>
                </section><!-- /.content -->
            </div>
             <footer class="main-footer">

                 @if(!\Event::dispatch('helpdesk.apply.whitelabel'))
                 <div style="position: fixed;right:0;bottom:0">
                    <button data-toggle="control-sidebar" onclick="openSlide()" style="margin-right:20px"  class="btn btn-primary helpsection">
                        {!! Lang::get('lang.have_a_question') !!}
                   &nbsp;&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></button>
                </div>
                @endif


                <div class="pull-right hidden-xs">
                     @if(\Event::dispatch('helpdesk.apply.whitelabel'))
                    <b>Version</b> {!! Config::get('app.tags') !!}

                    @else
                    <b>Version</b> {!! Config::get('app.version') !!}

                    @endif



                </div>
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
                    <li class="remov"><a href="javascript:void(0)" onclick="openSlide()" class="help-widget-close" style="background-color: black;height: 42px;"><i class="fa fa-times" aria-hidden="true" style="padding-left: 15px"></i></a></li>
                    <li ><a href="#settings-tab" data-toggle="tab" id="trapezoid" style="background-color:transparent;color:#444;" ><i class="fa fa-paper-plane-o" aria-hidden="true" ></i> {!! Lang::get('lang.mail_us') !!}
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
                            <div style="text-align:center"><h3 class="space-on-bottom-lg" style="color:black">{!! Lang::get('lang.prefer_email_instead?') !!}</h3>
                                <div><button class="btn btn-default space-on-bottom-20px space-on-top-10px" onclick="mailTab()">
                                        <span class="icon icon-envelope icon-gray tail5px"></span>{!! Lang::get('lang.write_to_us') !!} </button>
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
                                              <div class="col-sm-12" style="padding-left:37px">
                                                <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    <i class="menu-icon fa fa-ticket bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                    <h4 class="control-sidebar-subheading" style="padding-top: 10px">

                                                      <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                            {!! Lang::get('lang.create_ticket') !!}
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
                                                 <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    <i class="menu-icon fa fa-reply-all bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12" >

                                                   <h4 class="control-sidebar-subheading" style="padding-top: 10px">

                                                       <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                            {!! Lang::get('lang.canned_response') !!}
                                                        </a>
                                                    </h4>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12" style="padding-left: 30px;">
                                                  <a href="https://support.faveohelpdesk.com/show/user-organizations" target="_blank">
                                                    <i class="menu-icon fa fa-database bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                    <h4 class="control-sidebar-subheading" style="line-height: 2.5">
                                                        <a href="https://support.faveohelpdesk.com/show/user-organizations" target="_blank">
                                                            {!! Lang::get('lang.organization') !!}
                                                        </a>
                                                    </h4>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12" style="padding-left: 30px;">
                                                <a href="https://support.faveohelpdesk.com/search?s=dashboard" target="_blank">
                                                    <i class="menu-icon fa fa-desktop bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                    <h4 class="control-sidebar-subheading" style="line-height: 2.5">
                                                       <a href="https://support.faveohelpdesk.com/search?s=dashboard" target="_blank">
                                                            {!! Lang::get('lang.dashboard') !!}
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
                                                <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    <i class="menu-icon fa fa-external-link bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                    <h4 class="control-sidebar-subheading" style="padding-top: 10px">

                                                    <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    {!! Lang::get('lang.assign_ticket') !!}
                                                    </a>
                                                    </h4>
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
                                                <div class="col-sm-12" style="padding-left:40px">
                                                 <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    <i class="menu-icon fa fa-user-plus bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12" style="text-align: center;">
                                                    <h4 class="control-sidebar-subheading" style="padding-top: 10px">
                                                    <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    {!! Lang::get('lang.add_collaborator') !!}
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
                                                <a href="https://support.faveohelpdesk.com/search?s=user" target="_blank">
                                                    <i class="menu-icon fa fa-group bg-light-blue"></i>
                                                </a>
                                                </div>
                                                <div class="menu-info col-sm-12" style="padding-left:24px">

                                                    <h4 class="control-sidebar-subheading" style="padding-top: 10px">
                                                    <a href="https://support.faveohelpdesk.com/search?s=user" target="_blank">
                                                    {!! Lang::get('lang.user') !!}

                                                    </a>
                                                    </h4>
                                                         </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12" style="padding-left:35px">
                                                 <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    <i class="menu-icon fa fa-code-fork bg-light-blue"></i>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                    <h4 class="control-sidebar-subheading" style="padding-top: 10px">
                                                    <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    {!! Lang::get('lang.marge_ticket') !!}
                                                    </a>
                                                    </h4>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12" style="padding-left:35px">
                                                 <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    <i class="menu-icon fa fa-cogs bg-light-blue"></i>
                                               </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                     <h4 class="control-sidebar-subheading" style="padding-top: 10px">
                                                   <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    {!! Lang::get('lang.reopen_ticket') !!}
                                                    </a>
                                                    </h4>
                                                </div>
                                            </a>
                                        </li>
                                    </div>
                                    <div class="col-sm-2">
                                        <li class="category-tile">
                                            <a href="javascript:void(0)">
                                                <div class="col-sm-12" style="padding-left:30px">
                                                <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    <span class="menu-icon glyphicon glyphicon-paperclip bg-light-blue"></span>
                                                    </a>
                                                </div>
                                                <div class="menu-info col-sm-12">
                                                 <h4 class="control-sidebar-subheading" style="padding-top: 10px">
                                                <a href="https://support.faveohelpdesk.com/knowledgebase" target="_blank">
                                                    {!! Lang::get('lang.attachment') !!}
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




                        <div class="col-sm-6" style="border-left:1px solid gainsboro;height:346px;padding-top: 45px" ng-controller="helptopicCtrl">
                            <ng-form method="post" style="margin-top: 25px" id="helpForms">
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
                                        <div style="float:left">
                                        <div class="fileupload-wrapper" style="position: relative; overflow: hidden; direction: ltr;"><a href="#" tabindex="-1" class="file-attach-link" style="color: #00c0ef;">
                                        <i class="glyphicon glyphicon-paperclip small"></i>{!! Lang::get('lang.attachment') !!}</a>
                                                <input qq-button-id="9f785c98-e24f-476a-ad1d-790faf404528" enctype ='multipart/form-data' multiple="true" type="file" name="help_doc" id="helpdocs" title="File input" files='true' style="position: absolute; right: 0px; top: 0px; font-family: Arial; font-size: 118px; margin: 0px; padding: 0px; cursor: pointer; opacity: 0; height: 100%;"></div></div>
                                                <br>
                                                <div class='uploadFiles'></div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="btn-group" >
                                            <button class="btn btn-primary" id="myButton1" ng-click="helpForm()" disabled="disabled">{!! Lang::get('lang.send') !!}</button>

                                                <div class="load"></div>
                                        </div>
                                    </div>
                                </div>
                            </ng-form>
                        </div>


                        <!-- /.tab-pane -->
                    </div>



            </aside>

        </div><!-- ./wrapper -->

        <script  type="text/javascript">
            localStorage.setItem('PATH', '{{asset("/")}}');
            localStorage.setItem('CSRF', '{{ csrf_token() }}');
            localStorage.setItem('NOTI_COND', '{{$in_app_notification}}');
            localStorage.setItem('GUEST', '{{Auth::guest()}}');
            localStorage.setItem('LANGUAGE', '{{Lang::getLocale()}}');
            localStorage.setItem('PLUGIN', '{{isPlugin()}}');
            localStorage.setItem('SEGMENT', '{{$segment}}');

            //tabs
                //var segment = '<?= $segment ?>';
                var user=0;
                var tool=0;
                $('.tabs').find('a').on('click',function(){
                     var tab=$(this).html();
                   if(tab=="{!! Lang::get('lang.users') !!}"){

                     user++;
                        if(user%2==0){

                           $('.content-header').prevUntil('.tab-content').remove();
                           $('#slideUp').slideUp();
                        }
                        else{

                           $('.tab-content + .content-header').before($('<br><br>'));
                            $('#slideUp').slideDown();
                            tool=0;
                        }
                    }
                    if(tab=="{!! Lang::get('lang.tools') !!}"){
                     tool++;
                        if(tool%2==0){
                           $('.content-header').prevUntil('.tab-content').remove();
                           $('#slideUp').slideUp();
                        }
                        else{
                           $('.tab-content + .content-header').before($('<br><br>'));
                            $('#slideUp').slideDown();
                            user=0;
                        }
                    }
                })
$.ajaxSetup({
    headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
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

        <!-- Bootstrap 3.4.1 JS -->
        <script src="{{assetLink('js','bootstrap-latest')}}" type="text/javascript"></script>
        <!-- Slimscroll -->
        <script src="{{assetLink('js','nprogress')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','tabby')}}" type="text/javascript"></script>
        <!-- full calendar-->

        <script src="{{assetLink('js','moment')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','app-min')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','daterangepicker-min')}}" type="text/javascript" ></script>
        <script src="{{assetLink('js','moment')}}"></script>
        <script src="{{assetLink('js','moment-timezone')}}"></script>
        <script type="text/javascript" src="{{assetLink('js','moment-timezone-with-data')}}"></script>
        <script src="{{assetLink('js','ng-file-upload-shim')}}" ></script>
        <script src="{{assetLink('js','angular')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','angular-moment')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','bsSwitch')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','angular-desktop-notification')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','angular-recaptcha')}}" type="text/javascript"></script>
        <script src="{{assetLink('js','ng-flow-standalone')}}" ></script>
        <script src="{{assetLink('js','fusty-flow')}}" ></script>
        <script src="{{assetLink('js','fusty-flow-factory')}}" ></script>
        <script src="{{assetLink('js','ng-file-upload')}}"></script>
        <script src="{{assetLink('js','ng-file-upload-shim')}}"></script>
        <script src="{{assetLink('js','tw-currency-select')}}"></script>
        <script data-require="ui-bootstrap-tpls@1.2.5" data-semver="1.2.5" src="{{assetLink('js','ui-bootstrap-tpls')}}"></script>
        <script src="{{assetLink('js','angular-agent-scripts')}}" type="text/javascript"></script>

        <?php Event::dispatch('show.calendar.script', array()); ?>
        <?php Event::dispatch('load-calendar-scripts', array()); ?>
        <?php \Event::dispatch('timeline-customjs', [['fired_at' => 'agentlayout','request' => Request()]]); ?>
                @yield('FooterInclude')
                @stack('scripts')
    </body>
</html>
