@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.language_add-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.language_add-page-description') !!}">

@stop

@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('languages')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.language') !!}</h1>
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
{!! Form::open(array('url'=>'language/add' , 'method' => 'post', 'files'=>true,'id'=>'Form') )!!}
@if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fa  fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p>{{Session::get('success')}}</p>
        </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
            @if(Session::has('link'))
            <a href="{{url(Session::get('link'))}}">{{Lang::get('lang.enable_lang')}}</a>
            @endif
            @if(Session::has('link2'))
            <a href="{{url(Session::get('link2'))}}" target="blank">{{Lang::get('lang.read-more')}}</a>
            @endif
        </div>
        @endif
        @if(Session::has('errors'))
        <?php //dd($errors); ?>
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <br/>
            @if($errors->first('language-name'))
            <li class="error-message-padding">{!! $errors->first('language-name', ':message') !!}</li>
            @endif
            @if($errors->first('iso-code'))
            <li class="error-message-padding">{!! $errors->first('iso-code', ':message') !!}</li>
            @endif
            @if($errors->first('File'))
            <li class="error-message-padding">{!! $errors->first('File', ':message') !!}</li>
            @endif
        </div>
        @endif

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{{Lang::get('lang.add-lang-package')}}</h3>
    </div>
    <div class="box-body">
        
        <?php

        $language= \Config::get('languages');
        $lang = [];
        foreach ($language as $key => $value) {
            $src = $key.'.png';
            if (Lang::getLocale() == "ar") {
                $lang[$key] = $value[0].' &nbsp;&rlm;('.$value[1].')';
            } else {
                $lang[$key] = $value[0].' &nbsp;('.$value[1].')';
            }
        }
        ?>
        <div class="row">
            <!-- username -->
            <div class="col-xs-4 form-group {{ $errors->has('language-name') ? 'has-error' : '' }}">
                {!! Form::label('language-name',Lang::get('lang.language-name')) !!} <span class="text-red"> *</span>

               {!!Form::select('language-name', ['' => Lang::get('lang.select_language'), Lang::get('lang.language')=>$lang],null,['class' => 'form-control select','id'=>'select_lang']) !!}
            </div>
            <div class="col-xs-4 form-group {{ $errors->has('iso-code') ? 'has-error' : '' }}">
                {!! Form::label('iso-code',Lang::get('lang.iso-code')) !!} <span class="text-red"> *</span>
                {!! Form::text('iso-code',null,['placeholder'=>'en','class' => 'form-control','id'=>'iso-code','readonly'=>'readonly']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4 form-group {{ $errors->has('File') ? 'has-error' : '' }}">
                {!! Form::label('File',Lang::get('lang.file')) !!} <span class="text-red"> *</span>&nbsp;
                <div class="btn bg-olive btn-file" style="color:blue"> {!! Lang::get('lang.upload_file') !!}
                    {!! Form::file('File', ['id' => "upload"]) !!}
                </div>
                <span id="filename"></span>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <button type="submit" id="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i>{!! Lang::get('lang.saving') !!}"><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
    </div>
    {!! Form::close() !!}
</div>

<script type="text/javascript">
    $(document).ready(function () {
     
        $("#select_lang").on("change", function () {
             selected_lang = $("#select_lang").val();
             // alert(selected_lang);
             $.ajax({
                url: '{{route("add-language.iso-code")}}',
                data: {'selected_lang': selected_lang},
                type: "GET",
                dataType: "html",
                beforeSend: function() {
                    $('.loader').css('display','block');
                },
                success: function (data) {
                    $('.loader').css('display','none');
                    $("#iso-code").empty();
                    $('#iso-code').val(data); 
                     
                },
                
            });
            
        });
        });
</script>
<script type="text/javascript">
document.getElementById('upload').onchange = uploadOnChange; 
function uploadOnChange() {
    var filename = this.value;
    var lastIndex = filename.lastIndexOf("\\");
    if (lastIndex >= 0) {
        filename = filename.substring(lastIndex + 1);
    }
    $('#filename').html(filename);
}
</script>
@stop