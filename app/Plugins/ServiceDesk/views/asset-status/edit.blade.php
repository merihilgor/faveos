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

<meta name="title" content="{{trans('ServiceDesk::lang.asset_statuses_edit-page-title')}}  :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{{trans('ServiceDesk::lang.asset_statuses_edit-page-description')}} ">

@stop

@section('PageHeader')
    <h1>{{trans('ServiceDesk::lang.asset_status')}}</h1>
@stop

<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>

@section('content')
    
    <div id="app-sevicedesk">
        
        <asset-status-create-edit></asset-status-create-edit>
    </div>

    <script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop