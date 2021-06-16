@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.listeners') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.listeners') !!}">


@stop
@section('custom-css')


<link href="{{assetLink('css','bootstrap-switch-min')}}" rel="stylesheet" type="text/css" >
<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css" >
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
    .select2-container{
        width: 100% !important;
    }
    .radio-inline{
        padding-top: 0px;
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
    .angular-ui-tree-empty{
        border: none !important;
        background-image: none !important;
        background: transparent;
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

@section('listener')
class="active"
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.listeners')}} </h1>
@stop

@section('content')

<div class="box box-primary">
    <div class="box-container">
        <div class="box-header with-border">
            <h3 class="box-title">{!! Lang::get('lang.edit_listener') !!}</h3>
        </div>

        <div class="box-body" id="app-admin">
            <workflow-listener category="listener"></workflow-listener>
        </div>
    </div>
</div>

@stop
@push('scripts')
<script src="{{assetLink('js','bootstrap-switch-min')}}" type="text/javascript"></script>
<script src="{{assetLink('js','select2')}}"  type="text/javascript"></script><script>
@endpush
