@extends('themes.default1.agent.layout.agent')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        
        ?>
<style>
    .table{
        width: 100% !important; 
    },
    .left-hand{
        float: left;
    }
</style>
@section('meta-tags')
<meta name="title" content="{{Lang::get('ServiceDesk::lang.changes_view-page-title')}}  :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{{Lang::get('ServiceDesk::lang.changes_view-page-description')}} ">

@stop

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop

@section('PageHeader')
    <h1> {{Lang::get('ServiceDesk::lang.changes')}} </h1>
@stop

<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>

@section('content')

    <div id="app-sevicedesk">

        <changes-view></changes-view>
    </div>

    <script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop