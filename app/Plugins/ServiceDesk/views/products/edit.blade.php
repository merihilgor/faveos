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
<meta name="title" content="{{Lang::get('ServiceDesk::lang.products_edit-page-title')}}  :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{{Lang::get('ServiceDesk::lang.products_edit-page-description')}} ">

@stop

@section('custom-css')
   <style>
        .control-label:after {
            content:"*";color:red;
        }
    </style>
@stop

@section('PageHeader')
    <h1>{{Lang::get('ServiceDesk::lang.edit_product')}}</h1>
@stop

<style type="text/css">
    .cke_chrome {
     display: none !important;  
    }
</style>

<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>

@section('content')
    
    <div id="app-sevicedesk">
        
        <product-create-edit></product-create-edit>
    </div>
    
    <script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop

