@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.system-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.system-page-description') !!}">

@stop

@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('system')
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
<!-- /breadcrumbs -->
<!-- content -->
@section('content')

<div class="alert alert-success alert-dismissable" style="display: none;">
    <i class="fa  fa-check-circle"></i>
    <span class="success-msg"></span>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

</div>
<!-- open a form -->
{!! Form::model($systems,['url' => 'postsystem/'.$systems->id, 'method' => 'PATCH' , 'id'=>'formID','id'=>'Form']) !!}
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
        <?php //dd($errors); ?>
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <br/>
        </div>
        @endif
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{{Lang::get('lang.system-settings')}}</h3>
    </div>
    <!-- Helpdesk Status: radio Online Offline -->
    <div class="box-body">

        <div class="row">
            <!-- Helpdesk Name/Title: text Required   -->
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    {!! Form::label('name',Lang::get('lang.name/title')) !!}
                    {!! Form::text('name',$systems->name,['class' => 'form-control']) !!}
                    {!! $errors->first('name', '<spam class="help-block">:message</spam>') !!}
                </div>
            </div>
            <?php
            if(!$systems->url){
                $url = url('/');
            }else{
                $url= $systems->url;
            }
            ?>
             <!-- Helpdesk URL:      text   Required -->
             <div class="col-md-4">
                <div class="form-group {{ $errors->has('url') ? 'has-error' : '' }}">
                    {!! Form::label('url',Lang::get('lang.url')) !!}

                    <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.app_url_change_redirect_your_application_to_this_url') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                    {!! Form::text('url',$url,['class' => 'form-control']) !!}
                    {!! $errors->first('url', '<spam class="help-block">:message</spam>') !!}
                    {!! $errors->first('url', '<spam class="help-block">:message</spam>') !!}
                </div>
            </div>
            <!-- Helpdesk Access over ip -->
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('access_via_ip',Lang::get('lang.access_via_ip')) !!}
                     <a href="#" data-toggle="tooltip" title="{!! Lang::get('lang.access_via_ip_tooltip_message') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                    <div class="row">
                        <div class="col-xs-5">
                            {!! Form::radio('access_via_ip',1,true) !!} {{Lang::get('lang.yes')}}
                        </div>
                        <div class="col-xs-6">
                            {!! Form::radio('access_via_ip', 0) !!} {{Lang::get('lang.no')}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

         $google_service_key = commonSettings('google', 'service_key');
                    $google_secret_key = commonSettings('google secret key', 'secret_key');
        ?>

        <div class="row">
            <!-- Default Time Zone: Drop down: timezones table : Required -->

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('time_zone') ? 'has-error' : '' }}">
                    {!! Form::label('time_zone',Lang::get('lang.timezone')) !!}
                    {!!Form::select('time_zone',['Choose'=>$timezones],$selectedTimezone,['class'=>'form-control selectpicker','id'=>'tz','data-live-search'=>'true','data-live-search-placeholder'=>'Search','data-dropup-auto'=>'false','data-size'=>'5']) !!}
                    {!! $errors->first('time_zone', '<spam class="help-block">:message</spam>') !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('time_format') ? 'has-error' : '' }}">
                    <span>{!! Form::label('time_format',Lang::get('lang.time_format')).'&nbsp;<i class="pull-right" id="time-format-preview" ></i>' !!}
                    {!! Form::select('time_format', $systems->timeFormats, $systems->time_farmat, ['class' => 'form-control','id'=>'time_format']) !!}
                    {!! $errors->first('time_format', '<spam class="help-block">:message</spam>') !!}
                </div>
            </div>
              
              <div class="col-md-4">
                <div class="form-group {{ $errors->has('date_format') ? 'has-error' : '' }}">
                    <span>{!! Form::label('date_format',Lang::get('lang.date_format')).'&nbsp;<i class="pull-right" id="date-format-preview" ></i>' !!}
                    {!! Form::select('date_format', $systems->dateFormats, $systems->date_format,['class' => 'form-control','id'=>'date_format']) !!}
                    {!! $errors->first('date_format', '<spam class="help-block">:message</spam>') !!}
                </div>
            </div>
        </div>

        <div class="row">


             <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('status',Lang::get('lang.status')) !!}
                    <div class="row">
                        <div class="col-xs-5">
                            {!! Form::radio('status',1,true) !!} {{Lang::get('lang.online')}}
                        </div>
                        <div class="col-xs-6">
                            {!! Form::radio('status', 0) !!} {{Lang::get('lang.offline')}}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
               <div class="form-group">
                   {!! Form::label('status',Lang::get('lang.cdn')) !!}
                   <a href="#" data-toggle="tooltip" title="{{Lang::get('lang.cdn_tooltip')}}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                   <?php
                   $yes = false;
                   $no = false;

                   $inicialStatus = $common_setting->where('option_name','cdn_settings')->value('option_value');
                   if($inicialStatus == null){
                       $yes = true;
                   }
                   elseif($common_setting->where('option_name','cdn_settings')->value('option_value') == 1){
                       $yes = true;
                   }
                   else{
                       $no=true;
                   }
                   ?>
                   <div class="row">
                       <div class="col-xs-5">
                           {!! Form::radio('cdn',1,$yes) !!}&nbsp;&nbsp; {!! Lang::get('lang.on') !!}
                       </div>
                       <div class="col-xs-6">
                           {!! Form::radio('cdn',0,$no) !!}&nbsp;&nbsp; {!! Lang::get('lang.off') !!}
                           <a href="#" data-toggle="tooltip" title="{{Lang::get('lang.cdn_off_tooltip')}}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                       </div>
                   </div>
               </div>
           </div>
        </div>
      

      </div>
     <div class="box-footer">
        <button type="submit" class="btn btn-primary" style="border-radius: 0;" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> {!! Lang::get('lang.saving') !!}"><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
     </div>
</div>
{!! Form::close() !!}

@stop
@push('scripts')
<script>

    $(document).ready(function(){
        var tz =$("#tz option:selected").text();
        date($("#date_format").val(),tz.replace(/ *\([^)]*\) */g, ""), 'date-format-preview');
        date($("#time_format").val(),tz.replace(/ *\([^)]*\) */g, ""), 'time-format-preview');
    });

    $("#date_format").on('change',function(){
        var format = $("#date_format").val();
        var tz =$("#tz option:selected").text();
        date(format, tz.replace(/ *\([^)]*\) */g, ""), 'date-format-preview');
    });

    $("#time_format").on('change',function(){
        var format = $("#time_format").val();
        var tz =$("#tz option:selected").text();
        date(format, tz.replace(/ *\([^)]*\) */g, ""), 'time-format-preview');
    });

    function date(format, tz, identifier){
        $.ajax({
            url:"{{url('date/get')}}",
            dataType:"html",
            data : "format="+format+"&tz="+tz,
            success:function(date){
                $("#"+identifier).html("(e.g "+date+")");
            }
        });
    }

</script>
<link rel="stylesheet" href="{{assetLink('css','bootstrap')}}">
<link rel="stylesheet" href="{{assetLink('css','bootstrap-select')}}" />
 <script src="{{assetLink('js','bootstrap-select')}}"></script>
@endpush
