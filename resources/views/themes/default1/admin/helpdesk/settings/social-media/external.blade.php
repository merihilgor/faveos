@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        
        ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.social-login-settings-page-title') !!} :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{!! Lang::get('lang.social-login-settings-page-description') !!}">

@stop
@section('custom-css')
<link href="{{assetLink('css','faveo-css')}}" rel="stylesheet" type="text/css" />
@stop
@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('social-login')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h3>{!! Lang::get('lang.social-login') !!}</h3>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">

</ol>
@stop
@section('content')

 @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- check whether success or not -->
            @if(Session::has('success'))
                <div class="alert alert-success alert-dismissable">
                    <i class="fa  fa-check-circle"></i>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {!!Session::get('success')!!}
                </div>
            @endif
            <!-- failure message -->
            @if(Session::has('fails'))
                <div class="alert alert-danger alert-dismissable">
                    <i class="fa fa-ban"></i>
                    <b>{!! Lang::get('lang.alert') !!}!</b>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {!!Session::get('fails')!!}
                </div>
            @endif
            
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{!! Lang::get('lang.external_login') !!} {{Lang::get('lang.settings')}}</h3>
            <a class="btn btn-primary pull-right" target="_blank" href="http://google.com" title="{!! Lang::get('Lang.external_login_help_title') !!}">{!! Lang::get('lang.need_help') !!}&nbsp;<span class="fa fa-question-circle"></span></a>
        </div>
        {!! Form::open(['url' => 'social/external-login', 'method' => 'POST', 'id'=>'Form']) !!}
        <div class="box-body">
           
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('client_id') ? 'has-error' : '' }}">
                        {!! Form::label('name',Lang::get('lang.allow_external_login')) !!}
                        <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.allow_external_login_tooltip') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>&nbsp;&nbsp;
                        {!! Form::radio($external_login_settings['allow_external_login']['option_name'],1,$external_login_settings['allow_external_login']['status'], ['onClick' => "toggleFields(1)"]) !!} {{Lang::get('lang.yes')}}&nbsp;&nbsp;
                        {!! Form::radio($external_login_settings['allow_external_login']['option_name'],0,!$external_login_settings['allow_external_login']['status'], ['onClick' => "toggleFields(0)"]) !!} {{Lang::get('lang.no')}}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('client_id') ? 'has-error' : '' }}">
                        {!! Form::label('name',Lang::get('lang.allow_users_to_access_system_url')) !!}
                        <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.allow_users_to_access_system_url_tooltip') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>&nbsp;&nbsp;
                        {!! Form::radio($external_login_settings['allow_users_to_access_system_url']['option_name'], 1,$external_login_settings['allow_users_to_access_system_url']['status']) !!} {{Lang::get('lang.yes')}}&nbsp;&nbsp;
                        {!! Form::radio($external_login_settings['allow_users_to_access_system_url']['option_name'], 0,!$external_login_settings['allow_users_to_access_system_url']['status']) !!} {{Lang::get('lang.no')}}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('validate_api_parameter') ? 'has-error' : '' }}">
                        {!! Form::label('validate_api_parameter',Lang::get('lang.validation_api_parameter')) !!}<span class="required_field_star" style="color:red">*</span>
                        <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.validation_api_parameter_tooltip') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                        {!! Form::text($external_login_settings['validate_api_parameter']['option_name'], $external_login_settings['validate_api_parameter']['option_value'],['class' => 'form-control', 'required' => true, 'id' => 'api_parameter']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('validate_token_api') ? 'has-error' : '' }}">
                        {!! Form::label('validate_token_api',Lang::get('lang.validation_api_url')) !!}<span class="required_field_star" style="color:red">*</span>
                        <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.validation_api_url_tooltip') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                        {!! Form::url($external_login_settings['validate_token_api']['option_name'], $external_login_settings['validate_token_api']['option_value'],['class' => 'form-control', 'required' => true, 'id' => 'api_url']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('redirect_unauthenticated_users_to') ? 'has-error' : '' }}">
                        {!! Form::label('redirect_unauthenticated_users_to',Lang::get('lang.user_redirection_url')) !!}<span class="required_field_star" style="color:red">*</span>
                        <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.user_redirection_url_tooltip') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                        {!! Form::url($external_login_settings['redirect_unauthenticated_users_to']['option_name'], $external_login_settings['redirect_unauthenticated_users_to']['option_value'],['class' => 'form-control', 'required' => true, 'id' => 'redirect']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i>{!! Lang::get('lang.saving') !!}"><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
        </div>
        {!! Form::close() !!}
    </div>
@stop
@push('scripts')
<script type="text/javascript">
    function toggleFields(value) {
        if (value) {
            $('#api_parameter, #api_url, #redirect').prop('readonly', false);
            $('#api_parameter, #api_url, #redirect').prop('required', true);
            $('.required_field_star').css('display', 'inline-block');
        } else {
            $('#api_parameter, #api_url, #redirect').prop('readonly', true);
            $('#api_parameter, #api_url, #redirect').prop('required', false);
            $('.required_field_star').css('display', 'none');
        }
    }
    $( document ).ready(function() {
        @if($external_login_settings['allow_external_login']['status'])
            toggleFields(1);
            return 1;
        @endif
        toggleFields(0);
    });
</script>
@endpush
