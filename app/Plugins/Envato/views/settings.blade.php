@extends('themes.default1.admin.layout.admin')

@section('Plugins')
class="active"
@stop

@section('plugin-bar')
active
@stop

@section('Envato')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>Envato</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">

</ol>
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')


<!-- open a form -->

{!! Form::model($settings,['url' => 'postsettings', 'method' => 'PATCH','files'=>true]) !!}

<!-- <div class="form-group {{ $errors->has('company_name') ? 'has-error' : '' }}"> -->
<!-- table  -->
   @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fa  fa-check-circle"></i>
            <b>Success!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>Alert!</b> Failed.
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
<div class="box box-primary">
    <div class="box-header">
        <h4 class="box-title">Envato settings</h4>
    </div>

    <!-- check whether success or not -->

    <!-- Name text form Required -->
    <div class="box-body">
     
        <?php
        $ctrl = new App\Plugins\Envato\Controllers\EnvatoAuthController();
        $uri  = $ctrl->getCodeUrl();
        ?>
        



        <div class="">



            <div class="col-md-6 form-group {{ $errors->has('token') ? 'has-error' : '' }}">

                {!! Form::label('token','Application secret key') !!}
                {!! $errors->first('token', '<spam class="help-block">:message</spam>') !!}
                {!! Form::text('token',null,['class' => 'form-control']) !!}

            </div>

            <div class="col-md-6 form-group {{ $errors->has('client_id') ? 'has-error' : '' }}">

                {!! Form::label('client_id','Client') !!}
                {!! $errors->first('client_id', '<spam class="help-block">:message</spam>') !!}
                {!! Form::text('client_id',null,['class' => 'form-control']) !!}

            </div>

        </div>
        <div class="">


            <div class="col-md-6 form-group {{ $errors->has('mandatory') ? 'has-error' : '' }}">


                {!! Form::label('mandatory','Only for valid Envato purchase') !!}<br>
                <span> {!! Form::radio('mandatory',1,true) !!}&nbsp;Yes</span>&nbsp;&nbsp;&nbsp;&nbsp;
                <span> {!! Form::radio('mandatory',0,null) !!}&nbsp;No</span>

            </div>
            <div class="col-md-6 form-group {{ $errors->has('allow_expired') ? 'has-error' : '' }}">


                {!! Form::label('allow_expired','Allow expired support') !!}<br>
                <span>{!! Form::radio('allow_expired',1,true) !!}&nbsp;Yes</span>&nbsp;&nbsp;&nbsp;&nbsp;
                <span>{!! Form::radio('allow_expired',0,null) !!}&nbsp;No</span>


            </div>
            {!! Form::close() !!}



        </div>
        <div class="row">


            <div class="col-md-6 form-group" style="margin-left: 15px;">


                {!! Form::label('callback','Callback URI') !!}<br>
                {!! Form::text('callback',url('envato/redirect'),['class' => 'form-control','disabled'=>true]) !!}

            </div>
            @if($settings && !$settings->access_token)

            <div class="col-md-6 form-group">

                <a class='btn btn-block btn-social btn-primary' href='{{$uri}}' style='background-color: #55ACEE;color: white;'>
                    <span class='fa fa-clock'></span> Connect to Envato
                </a>
            </div>
            @endif


        </div>
        
        @if($settings && $settings->access_token)
        <div class="">
            <div class="col-md-6 form-group">
                <a class='btn btn-block btn-social btn-primary' href='{{$uri}}' style='background-color: #55ACEE;color: white;'>
                    <span class='fa fa-clock'></span> Connected as {{$ctrl->getAccount('firstname')}}
                </a>
            </div>
            <div class="col-md-6 form-group">
                <a class='btn btn-block btn-social btn-primary' href='{{$uri}}' style='background-color: #55ACEE;color: white;'>
                    <span class='fa fa-clock'></span> Reconnect to Envato
                </a>
            </div>
        </div>
        @endif

        <div class="box-footer form-group" style="margin-left:8px">
<!--            {!! Form::submit(Lang::get('lang.save'),['class'=>'form-group btn btn-primary'])!!}-->
                  <button type="submit" class="form-group btn btn-primary"><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>Save</button>
        </div>
        
        @stop

        @section('FooterInclude')

        @stop

        <!-- /content -->
