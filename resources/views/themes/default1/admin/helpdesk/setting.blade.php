@extends('themes.default1.admin.layout.admin')
@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.admin_panel') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
<!-- failure message -->
@if(Session::has('fails'))
<div class="alert alert-danger alert-dismissable">
    <i class="fa fa-ban"> </i> <b> {!! Lang::get('lang.alert') !!}! </b>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {!! Session::get('fails')!!}
</div>
@endif
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('lang.staffs') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('agents') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-user fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.agents') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('departments') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-sitemap fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.departments') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('teams') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-users fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.teams') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <?php \Event::dispatch('settings.agent.view', []); ?>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>
<!-- /.box -->

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('lang.email') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('emails') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-envelope-o fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.emails_settings') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('template-sets') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-file-text-o fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.templates') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--/.col-md-2-->
                {{--<div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('getemail')}}" onclick="sidebaropen(this)">
                <span class="fa-stack fa-2x">
                    <i class="fa fa-at fa-stack-1x"></i>
                </span>
                </a>
            </div>
            <p class="box-title" >{!! Lang::get('lang.email-settings') !!}</p>
        </div>
    </div>--}}
    <div class="col-md-2 col-sm-6">
        <div class="settingiconblue">
            <div class="settingdivblue">
                <a href="{{url('queue')}}" onclick="sidebaropen(this)">
                    <span class="fa-stack fa-2x">
                        <i class="fa fa-upload fa-stack-1x"></i>
                    </span>
                </a>
            </div>
            <p class="box-title" >{!! Lang::get('lang.queues') !!}</p>
        </div>
    </div>
    <!--col-md-2-->
    <div class="col-md-2 col-sm-6">
        <div class="settingiconblue">
            <div class="settingdivblue">
                <a href="{{ url('getdiagno') }}" onclick="sidebaropen(this)">
                    <span class="fa-stack fa-2x">
                        <i class="fa fa-plus fa-stack-1x"></i>
                    </span>
                </a>
            </div>
            <p class="box-title" >{!! Lang::get('lang.diagnostics') !!}</p>
        </div>
    </div>
    <!--/.col-md-2-->
</div>
</div>
<!-- /.row -->
</div>
<!-- ./box-body -->
</div>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('lang.manage') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('helptopic')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-file-text-o fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.help_topics') !!}</p>
                    </div>
                </div>

                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('sla')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-clock-o fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.sla_plans') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->


                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('sla/business-hours/index')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-calendar fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.business_hours') !!}</p>
                    </div>
                </div>

                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('forms/create')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-file-text fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.form-builder') !!}</p>
                    </div>
                </div>

                <!--/.col-md-2-->
                <!-- <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('form/user')}}">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-user-plus fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.requester-form') !!}</p>
                    </div>
                </div> -->
                <!--/.col-md-2-->

                
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('form-groups') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-object-group"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.form-groups') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('workflow')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-sitemap fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.workflow') !!}</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('approval-workflow')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-sitemap fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.approval-workflow') !!}</p>
                    </div>
                </div>
                <!-- priority -->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('ticket/priority')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">

                                    <i class="fa fa-asterisk fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.priority') !!}</p>
                    </div>
                </div>

                <!-- Ticket Types -->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('ticket-types')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">

                                    <i class="fa fa-list-ol fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.ticket_type') !!}</p>
                    </div>
                </div>

                <!--/.col-md-2-->

                <div class="col-md-2 col-sm-6 hide">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="url('url/settings'">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-server fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >Url</p>

                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('listener')}}">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-magic fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.listeners') !!}</p>

                    </div>
                </div>

                <!--/.col-md-2-->
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('widgets') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-list-alt fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.widgets') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('lang.ticket') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('getticket')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-ticket fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.ticket_settings') !!}</p>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('alert')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-bell-o fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.alert_notices') !!}</p>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('setting-status')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-plus-square-o"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('lang.status')}}</p>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('labels')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-lastfm"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('lang.labels')}}</p>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('getratings')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-star"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.ratings') !!}</p>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('close-workflow')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-sitemap"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.close_ticket_workflow') !!}</p>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('tag')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-tags"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.tags') !!}</p>
                    </div>
                </div>

                <?php \Event::dispatch('settings.ticket.view', []); ?>

                <div class="col-md-2 col-sm-6" >
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('source')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-gg"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.source') !!}</p>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6" >
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('recur/list')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-copy"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.recurring') !!}</p>
                    </div>
                </div>
                
                <?php \Event::dispatch('helpdesk.settings.ticket.location'); ?>

                @includeWhen(isTimeTrack(), 'timetrack::settings.icon')

            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('lang.settings') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{!! url('dashboard-settings') !!}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-dashboard fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title">{!! Lang::get('lang.dashboard-statistics') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{!! url('getcompany') !!}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-building-o fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.company') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('getsystem')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-laptop fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.system') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('user-options')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-user fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.user-options') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('social/media') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-globe fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('lang.social-login')}}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('languages')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-language fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title">{!! Lang::get('lang.language') !!}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-md-12">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('job-scheduler')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa  fa-hourglass-o fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.cron') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6" >
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('security')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa  fa-lock fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.security') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('settings-notification')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-bell"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.notification') !!}</p>
                    </div>
                </div>

                <?php \Event::dispatch('settings.system', []); ?>
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{url('system-backup')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-hdd-o"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.system-backup') !!}</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('widgets/social-icon') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa  fa-external-link fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.social-icon-links') !!}</p>
                    </div>
                </div>


                <!--/.col-md-2-->
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('api') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-cogs"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.api') !!}</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('webhook') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-server"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.webhook') !!}</p>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('websockets/settings') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-bolt"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.websockets') !!}</p>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('importer') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-download"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.importer_user_import') !!}</p>
                    </div>
                </div>

                
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('recaptcha') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-refresh fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" ><span style="font-variant: normal; font-size: 13px;">re</span>{!! Lang::get('lang.captcha') !!}</p>
                    </div>
                </div>
                <!--col-md-2-->
                <!--/.col-md-2-->
                @if($dummy_installation == 1 || $dummy_installation == '1')
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{route('clean-database')}}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-undo"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.delete_dummy_data') !!}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- ./box-body -->
</div>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('lang.add-ons') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('plugins') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-plug fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.plugin') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('modules') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                       <i class="fa fa-link"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.modules') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->

                <?php
                         $check_satellite_helpdesk = \App\Model\helpdesk\Settings\CommonSettings::where('option_name', '=', 'satellite_helpdesk')->select('status')->first();
                              ?>

                                   @if ($check_satellite_helpdesk && $check_satellite_helpdesk->status == 1)

                                    <?php \Event::dispatch('satellite-helpdesk.settings', array()); ?>

                                    @endif


                <!--/.col-md-2-->
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>

<?php \Event::dispatch('show.admin.settings', array());?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('lang.system-debug') !!}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ route('err.debug.settings') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-bug fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title">{!! Lang::get('lang.debug-options') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ route('system.logs') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-history fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.system-logs') !!}</p>
                    </div>
                </div>

                <!--/.col-md-2-->
                @if(getActiveQueue() == 'redis')
                <!--col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ route('horizon.index') }}" target="_blank">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-desktop fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{!! Lang::get('lang.queue-monitor') !!}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                @endif
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>

<?php \Event::dispatch('service.desk.admin.settings', array()); ?>
@stop
