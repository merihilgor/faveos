@extends('themes.default1.agent.layout.agent')
<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.organizations_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.organizations_edit-page-description') !!}">

@stop

@section('custom-css')

<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>

<style>
    .select2-container--default .select2-selection--multiple{
        border-radius: 0px !important;
        border: 1px solid #ccc !important;
    }
    .btn-bs-file{
    position:relative;
}
.btn-bs-file input[type="file"]{
    position: absolute;
    filter: alpha(opacity=0);
    opacity: 0;
    width:0;
    height:0;
    outline: none;
    cursor: inherit;
}
</style>
@stop
@section('Users')
class="active"
@stop

@section('user-bar')
active
@stop

@section('organizations')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.organisation') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
<!-- /breadcrumbs -->
<!-- content -->

<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>

@section('content')

@include('themes.default1.common.recaptcha')

<div id="app-agent">
    <organisation-form></organisation-form>
</div>
<script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop
@section('FooterInclude')
<script src="{{assetLink('js','select2')}}" type="text/javascript"></script>
@stop
