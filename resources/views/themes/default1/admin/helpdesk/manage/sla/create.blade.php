@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.sla_plans_create-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.sla_plans_create-page-description') !!}">

<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>

<script src="{{assetLink('js','select2')}}" type="text/javascript"></script>

@stop

@section('Manage')
active
@stop

<style type="text/css">
	
	.box-container {
    margin: 20px 15px !important;
    border: 1px solid #ddd;
	}

	.glyphicon-minus-sign{
    font-size: 25px!important;
    color: #990000;
	}
	.glyphicon-plus{
	   display: inline-block;
	  width: 23px;
	  height: 24px;
	  color:#ffffff!important;
	  border:5px solid #009853;
	  background-color: #009853;
	  border-radius: 50%; /* circle */
	}
</style>

@section('manage-bar')
active
@stop

@section('sla')
class="active"
@stop

@section('HeadInclude')

@stop

@section('PageHeader')
<h1>{!! Lang::get('lang.sla_plans') !!}</h1>
@stop
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
@section('custom-css')

@stop

@section('content')

    <div id="app-admin">
        <sla-create-edit></sla-create-edit>
    </div>

    <script src="{{assetLink('js','select2')}}"></script>
@stop
