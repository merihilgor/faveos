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
<meta name="title" content="{{trans('ServiceDesk::lang.asset_statuses_lists-page-title')}}  :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{{trans('ServiceDesk::lang.asset_statuses_lists-page-description')}} ">

@stop

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop

@section('PageHeader')
    <h1> {{trans('ServiceDesk::lang.asset_statuses')}}</h1>
@stop

@section('content')

    <div id="app-sevicedesk">

        <asset-status-index></asset-status-index>
    </div>

    <script src="{{ asset('js/serviceDesk.js') }}"></script>  
@stop