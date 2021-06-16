@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.workflow_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.workflow_lists-page-description') !!}">




@stop
@section('custom-css')
<link href="{{assetLink('css','bootstrap-switch-min')}}" rel="stylesheet"/>
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
<link href="{{assetLink('css','angular-ui-tree')}}" rel="stylesheet" type="text/css" >
<link href="{{assetLink('css','app-form')}}" rel="stylesheet" type="text/css" >

<style>
	select{
	   -webkit-appearance: none;
	   -moz-appearance:    none;
	   appearance:         none;
	   -webkit-border-radius: 0;  /* Safari 3-4, iOS 1-3.2, Android 1.6- */
	   -moz-border-radius: 0;  /* Firefox 1-3.6 */
	   border-radius: 0;  /* Opera 10.5, IE 9, Safari 5, Chrome, Firefox 4, iOS 4, Android 2.1+ */
	}
	.bootstrap-switch{
		border-radius: 0;
	}
	.bootstrap-switch .bootstrap-switch-handle-on{
		border-bottom-left-radius: 0px;
		border-top-left-radius: 0px;
	}
	.bootstrap-switch .bootstrap-switch-handle-off{
		border-bottom-right-radius: 0px !important;
		border-top-right-radius: 0px !important;
	}
	.select2-container--default .select2-selection--single .select2-selection__rendered{
		line-height: 32px;
	}
	.select2-container--default .select2-selection--single .select2-selection__arrow{
		height: 32px;
	}
	.select2-container .select2-selection--multiple,.select2-container--default .select2-selection--single{
		height: 34px;
		border-radius: 0px !important;
		border: 1px solid #d2d6de !important;
		overflow-y: auto;
		overflow-x: hidden;
	}
	.select2-container{
		width: 100% !important;
	}
	.radio-inline{
		padding-top: 0px;
	}
	.enab {
		z-index:2;
		color: #333 !important;
		background-color: #e6e6e6 !important;
		background-image: none !important;
		outline: 0 !important;
		-moz-box-shadow: inset 0 0px 2px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05) !important;
		-webkit-box-shadow: inset 0 0px 2px rgba(0,0,0,0.15), 0 1px 2px rgba(0,0,0,0.05) !important;
		box-shadow: inset 0 0px 2px rgba(0,0,0,0.15), 0 1px 2px rgba(0,0,0,0.05) !important;
	}
	input[type="file"]:focus, input[type="radio"]:focus, input[type="checkbox"]:focus {
		outline: none;
		outline-offset: -2px;
	}
	.angular-ui-tree-handle {
		background: transparent;
		border: none;
		color: transparent;
		padding: 0px 13px;
	}
	.nav-tabs-custom > .nav-tabs > li{
		margin-bottom: -3px;
		margin-right: 5px;
	}
	.select2-container--default .select2-results__option .select2-results__option{
		padding-left: 6px;
	}
	.select2-container--default .select2-selection--single .select2-selection__arrow{
		position: fixed;
	}
</style>
@stop
@section('Manage')
active
@stop

@section('manage-bar')
active
@stop

@section('workflow')
class="active"
@stop

@section('HeadInclude')
@stop

<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.workflow') !!}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
	<li><a href="{!! URL::route('setting') !!}"><i class="fa fa-dashboard"></i> {!! Lang::get('lang.home') !!}</a></li>
	<li><a href="{!! URL::route('workflow') !!}">{!! Lang::get('lang.ticket_workflow') !!}</a></li>
	<li class="active"><a href="{!! URL::route('workflow.create') !!}">{!! Lang::get('lang.create_workflow') !!}</a></li>
</ol>
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
<div class="box box-primary">
  <div class="box-container">
    <div class="box-header with-border">
        <h3 class="box-title">{!! Lang::get('lang.create_workflow') !!}</h3>
    </div>

    <div class="box-body" id="app-admin">
        <workflow-listener category="workflow"></workflow-listener>
    </div>
  </div>
</div>

<script>
     $(function () {
        $("[name='my-checkbox']").bootstrapSwitch();
    })
</script>
@stop
@push('scripts')
<script src="{{assetLink('js','bootstrap-switch-min')}}"></script>
<script src="{{assetLink('js','select2')}}"></script>
@endpush
