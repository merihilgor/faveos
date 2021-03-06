@extends('themes.default1.agent.layout.agent')
<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.edit-user-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.edit-user-description') !!}">

@stop
@section('custom-css')
<link href="{{assetLink('css','intlTelInput')}}" rel="stylesheet" type="text/css">
<!-- iCheck -->
<link href="{{assetLink('css','blue')}}" rel="stylesheet" type="text/css" />
<!-- Select2 -->
<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css" />

@stop
@section('Users')
class="active"
@stop

@section('user-bar')
active
@stop

@section('user')
class="active"
@stop

<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.user') !!}</h1>
@stop
<!-- /header -->
<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>
<!-- content -->
@section('content')

@include('themes.default1.common.recaptcha')

<div id="app-agent">
    <user-form></user-form>
</div>
<script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
<!--submit script-->

@stop
@section('FooterInclude')
<script src="{{assetLink('js','select2')}}"></script>
@stop
