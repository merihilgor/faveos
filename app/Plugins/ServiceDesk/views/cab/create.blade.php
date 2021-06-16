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
<meta name="title" content="{{Lang::get('ServiceDesk::lang.cab_create-page-title')}}  :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{{Lang::get('ServiceDesk::lang.cab_create-page-description')}} ">

@stop

@section('custom-css')
    <link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css" />
@stop
@section('PageHeader')
    <h1> {{Lang::get('ServiceDesk::lang.cabs')}} </h1>
@stop

@section('content')
    
    <div id="app-sevicedesk">
        <cab-create-edit></cab-create-edit>
    </div>

    <script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop