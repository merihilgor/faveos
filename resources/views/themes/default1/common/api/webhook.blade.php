@extends('themes.default1.admin.layout.admin')

<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (isset($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        
        ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.webhook_settings-page-title') !!} :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{!! Lang::get('lang.webhook_settings-page-description') !!}">

@stop

@section('Settings')
active
@stop

@section('webhook')
class=active
@stop

@section('settings-bar')
active
@stop


@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.webhook')}}</h1>
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
                @if(Session::has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{Session::get('success')}}
                </div>
                @endif
                <!-- fail message -->
                @if(Session::has('fails'))
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{Session::get('fails')}}
                </div>
                @endif
<div class="box box-primary">
    {!! Form::open(['url'=>'webhook','method'=>'post','files'=>true,'id'=>'Form']) !!}
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('lang.webhook_configurations') !!}</h3>     

    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
               
               
                <!-- Guest user page Content -->
               
            </div>
   
            <div class="col-md-12">
                <h4>{!! Lang::get('lang.webhooks') !!} ({!! Lang::get('lang.enter_url_to_send_ticket_details') !!})</h4>
                <!-- <div class="col-md-6">
                    <div class="form-group {{ $errors->has('create_ticket_detail') ? 'has-error' : '' }}">
                        {!! Form::label('create_ticket_detail',Lang::get('lang.create_event'),['class'=>'required']) !!} <i class="fa fa-question-circle" data-toggle='tooltip' title="{{Lang::get('lang.webhook_create_tooltip')}}"></i>
                        {!! Form::text('create_ticket_detail',checkArray('create_ticket_detail',$details),['class' => 'form-control','placeholder'=>'http://www.example.com']) !!}
                        <p></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('update_ticket_detail') ? 'has-error' : '' }}">
                        {!! Form::label('update_ticket_detail',Lang::get('lang.update_event'),['class'=>'required']) !!} <i class="fa fa-question-circle" data-toggle='tooltip' title="{{Lang::get('lang.webhook_update_tooltip')}}"></i>
                        {!! Form::text('update_ticket_detail',checkArray('update_ticket_detail',$details),['class' => 'form-control','placeholder'=>'http://www.example.com']) !!}
                        <p></p>
                    </div>
                </div> -->

                <div class="col-md-12">
                    <div class="form-group {{ $errors->has('web_hook') ? 'has-error' : '' }}">
                        {!! Form::label('web_hook',Lang::get('lang.web_hook_url'),['class'=>'required']) !!} <i class="fa fa-question-circle" data-toggle='tooltip' title="{{Lang::get('lang.webhook_update_tooltip')}}"></i>
                        {!! Form::text('web_hook',checkArray('web_hook',$details),['class' => 'form-control','placeholder'=>'http://www.example.com']) !!}
                        <p></p>
                    </div>
                </div>
            </div></div>
    </div>
    <div class="box-footer">
            <button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> {!! Lang::get('lang.saving') !!}"><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
    
    </div>
    {!! Form::close() !!}   
</div>
    
@stop
