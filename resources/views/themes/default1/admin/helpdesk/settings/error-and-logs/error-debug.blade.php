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
<meta name="title" content="{!! Lang::get('lang.error-debug-settings-page-title') !!} :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{!! Lang::get('lang.error-debug-settings-page-description') !!}">

@stop

@section('error-bugs')
active
@stop

@section('settings-bar')
active
@stop

@section('debugging-option')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.error-debug')}}</h1>
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
	<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{{Lang::get('lang.debug-options')}}</h3> 
    </div>
    <!-- Helpdesk Status: radio Online Offline -->
    <div class="box-body">
       
        {!! Form::open(['url' => route('post.error.debug.settings'), 'method' => 'POST','id'=>'Form']) !!}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('debug',Lang::get('lang.debugging')) !!}
                    <div class="row">
                        <div class="col-xs-5">
                            <input type="radio" name="debug" value="true" @if($debug == true) checked="true" @endif> {{Lang::get('lang.enable')}}
                        </div>
                        <div class="col-xs-5">
                            <input type="radio" name="debug" value="false" @if($debug == false) checked="true" @endif> {{Lang::get('lang.disable')}}
                        </div>
                    </div>
                </div> 
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('bugsnag',Lang::get('lang.bugsnag-debugging')) !!}
                    <div class="row">
                        <div class="col-xs-5">
                            <input type="radio" name="bugsnag" value="true" @if($bugsnag == true) checked="true" @endif> {{Lang::get('lang.yes')}}
                        </div>
                        <div class="col-xs-5">
                            <input type="radio" name="bugsnag" value="false" @if($bugsnag == false) checked="true" @endif> {{Lang::get('lang.no')}}
                        </div>
                    </div>
                </div> 
            </div>
        </div>
        </div>
         <div class="box-footer">
      
<!--        {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('lang.save'),['type' => 'submit', 'class' =>'btn btn-primary','onclick'=>'sendForm()'])!!}-->
             <button type="submit" class="btn btn-primary" id='submit'  data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> {!! Lang::get('lang.saving') !!}"><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
         
         </div>
     {!! Form::close() !!}  
        </div>
@stop