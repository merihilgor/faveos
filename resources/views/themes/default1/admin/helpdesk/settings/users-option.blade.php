@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.user-options-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.user-options-page-description') !!}">

@stop

@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('user-options')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.settings')}}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@Section('custom-css')
<!-- /breadcrumbs -->
<style type="text/css">
      ul.tagit {
        width: 450%!important;
    }
    .source-counter{
        margin-left: 0px !important;
    }

      .box-header{
        padding-bottom:4px;
        background-color: #eee;

    }

    .box-container{
        margin: 10px 10px 20px 10px;
        border: 1px solid #ddd;
        background-color: white;
         /*box-shadow: 5px 10px #888888;*/
    }

</style>
@stop
<!-- content -->
@section('content')
<!-- open a form -->
  <!-- check whether success or not -->
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissable">
                <i class="fa fa-check-circle"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {!!Session::get('success')!!}
            </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <b>{!! Lang::get('lang.alert') !!}!</b><br/>
            <li class="error-message-padding">{!!Session::get('fails')!!}</li>
        </div>
        @endif
        @if(Session::has('errors'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <br/>
        </div>
        @endif
<div class="box box-primary">
    <div class="box-header with-border" style="background-color: white;">
        <h3 class="box-title">{{Lang::get('lang.user_and_organization_settings')}}</h3>
    </div>
    <div class="box-container">
    <div class="box-header with-border">
        <h3 class="box-title">{{Lang::get('lang.user-options')}}</h3>
    </div>
    <div class="box-body">

        <div class="row">
                {!! Form::open(['url' => 'user-options', 'method' => 'POST' , 'id'=>'formID']) !!}
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('send_otp',Lang::get('lang.allow_unverified_users_to_create_ticket')) !!}
                        <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.unauth_user_ticket_create_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                        <div class="row">
                            <div class="col-xs-5">
                                <input type="radio" name="allow_users_to_create_ticket" value="1" @if($formattedArray['allow_users_to_create_ticket']['status'] == '1') checked="true" @endif>&nbsp;{{Lang::get('lang.yes')}}
                            </div>
                            <div class="col-xs-6">
                                <input type="radio" name="allow_users_to_create_ticket" value="0" @if($formattedArray['allow_users_to_create_ticket']['status'] == '0') checked="true" @endif>&nbsp;{{Lang::get('lang.no')}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('user_set_ticket_status',Lang::get('lang.user_set_ticket_status')) !!}
                        <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.user_set_ticket_status_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                        <div class="row">
                         <div class="col-xs-5">
                                <input type="radio" name="user_set_ticket_status" value="1" @if($formattedArray['user_set_ticket_status']['status'] == '1') checked="true" @endif>&nbsp;{{Lang::get('lang.yes')}}
                            </div>
                            <div class="col-xs-6">
                                <input type="radio" name="user_set_ticket_status" value="0" @if($formattedArray['user_set_ticket_status']['status'] == '0') checked="true" @endif>&nbsp;{{Lang::get('lang.no')}}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('send_otp',Lang::get('lang.user_show_cc_ticket')) !!}
                        <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.user_show_cc_ticket_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                        <div class="row">
                            <div class="col-xs-5">
                                <input type="radio" name="user_show_cc_ticket" value="1" @if($formattedArray['user_show_cc_ticket']['status'] == '1') checked="true" @endif>&nbsp;{{Lang::get('lang.yes')}}
                            </div>
                            <div class="col-xs-6">
                                <input type="radio" name="user_show_cc_ticket" value="0" @if($formattedArray['user_show_cc_ticket']['status'] == '0') checked="true" @endif>&nbsp;{{Lang::get('lang.no')}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('user_registration',Lang::get('lang.allow_users_registration')) !!}
                        <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.allow_users_registration_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                        <div class="row">
                            <div class="col-xs-5">
                                <input type="radio" name="user_registration" value="1" @if($formattedArray['user_registration']['status'] == '1') checked="true" @endif>&nbsp;{{Lang::get('lang.yes')}}
                            </div>
                            <div class="col-xs-6">
                                <input type="radio" name="user_registration" value="0" @if($formattedArray['user_registration']['status'] == '0') checked="true" @endif>&nbsp;{{Lang::get('lang.no')}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6">
                    {!! Form::label('user_registration',Lang::get('lang.user-account-activation-and-verifcation')) !!}
                        <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.user-account-activation-and-verifcation_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                    <div class="form-group">
            {!! Form::checkbox('login_restrictions[]','email', in_array('email', $aoption), ["class" => "aoption"]) !!}  {!! Lang::get('lang.actvate-by-email') !!}
        </div>
        <div class="form-group">
            <?php \Event::dispatch('mobile_account_verification_show', [[$aoption]]); ?>
        </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Organization options -->
    <div class="box-container">
    <div class="box-header with-border">
        <h3 class="box-title">{{Lang::get('lang.organization-options')}}</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('user_show_org_ticket',Lang::get('lang.allow_users_to_show_organization_tickets')) !!}
                    <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.allow_users_to_show_organization_tickets_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                    <div class="row">
                        <div class="col-xs-5">
                            <input type="radio" name="user_show_org_ticket" value="1" @if($formattedArray['user_show_org_ticket']['status'] == '1') checked="true" @endif>&nbsp;{{Lang::get('lang.yes')}}
                        </div>
                        <div class="col-xs-6">
                            <input type="radio" name="user_show_org_ticket" value="0" @if($formattedArray['user_show_org_ticket']['status'] == '0') checked="true" @endif>&nbsp;{{Lang::get('lang.no')}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('user_reply_org_ticket',Lang::get('lang.allow_users_to_reply_organization_tickets')) !!}
                    <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.allow_users_to_reply_organization_tickets_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                    <div class="row">
                        <div class="col-xs-5">
                            <input type="radio" name="user_reply_org_ticket" value="1" @if($formattedArray['user_reply_org_ticket']['status'] == '1') checked="true" @endif>&nbsp;{{Lang::get('lang.yes')}}
                        </div>
                        <div class="col-xs-6">
                            <input type="radio" name="user_reply_org_ticket" value="0" @if($formattedArray['user_reply_org_ticket']['status'] == '0') checked="true" @endif>&nbsp;{{Lang::get('lang.no')}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
            <div class="col-md-4" style="display: none;">
                <div class="form-group">
                    {!! Form::label('allow_organization_dept_mngr_approve_tickets',Lang::get('lang.allow_org_department_managers_to_approve_tickets')) !!}
                    <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.org_department_managers_to_approve_tickets_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                    <div class="row">
                        <div class="col-xs-5">
                            <input type="radio" name="allow_organization_dept_mngr_approve_tickets" value="1" @if($formattedArray['allow_organization_dept_mngr_approve_tickets']['status'] == '1') checked="true" @endif>&nbsp;{{Lang::get('lang.yes')}}
                        </div>
                        <div class="col-xs-6">
                            <input type="radio" name="allow_organization_dept_mngr_approve_tickets" value="0" @if($formattedArray['allow_organization_dept_mngr_approve_tickets']['status'] == '0') checked="true" @endif>&nbsp;{{Lang::get('lang.no')}}
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
</div>
    <!-- /Organization options -->
    <div class="box-footer">

        {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
    </div>
    {!! Form::close() !!}
</div>

@stop
