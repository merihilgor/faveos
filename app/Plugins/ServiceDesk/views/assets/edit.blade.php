@extends('themes.default1.agent.layout.agent')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        
        ?>
@section('meta-tags')
<meta name="title" content="{{Lang::get('ServiceDesk::lang.assets_create-page-title')}}  :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{{Lang::get('ServiceDesk::lang.assets_create-page-description')}} ">

@stop

@section('PageHeader')
    <h1> {{Lang::get('ServiceDesk::lang.assets')}} </h1>
@stop

<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>

@section('content')
    
<div id="app-sevicedesk">
    <asset-form panel="agent"></asset-form>
</div>

<script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop