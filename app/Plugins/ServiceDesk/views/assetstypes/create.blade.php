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
    <meta name="title" content="{{trans('ServiceDesk::lang.asset_type_create-page-title')}}  :: {!! strip_tags($title_name) !!} ">
    <meta name="description" content="{{trans('ServiceDesk::lang.asset_type_create-page-description')}} ">
@stop

@section('PageHeader')
    <h1>{{trans('ServiceDesk::lang.assetstypes')}}</h1>
@stop

@section('content')
    
    <div id="app-sevicedesk">
        <assetstypes-create-edit></assetstypes-create-edit>
    </div>

    <script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop
