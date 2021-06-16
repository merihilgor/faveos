@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        
        ?>
<style>
    .left-hand{
        float: left;
    }
</style>
@section('meta-tags')
<meta name="title" content="{{Lang::get('ServiceDesk::lang.products_view-page-title')}}  :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{{Lang::get('ServiceDesk::lang.products_view-page-description')}} ">

@stop

@section('PageHeader')
    <h1> {{Lang::get('ServiceDesk::lang.products')}} </h1>
@stop

@section('content')
    
     <div id="app-sevicedesk">
        <product-view></product-view>
    </div>

    <script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop