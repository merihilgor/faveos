@extends('themes.default1.admin.layout.admin')

@section('custom-css')
       <link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
@stop

<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        
        ?>
@section('meta-tags')
<meta name="title" content="{{Lang::get('ServiceDesk::lang.announcement-page-title')}}  :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{{Lang::get('ServiceDesk::lang.announcement-page-description')}} ">

@stop

@section('PageHeader')
  <h1> {{Lang::get('ServiceDesk::lang.announcement-page-title')}} </h1>
@stop

<script src="{{assetLink('js','ckeditor')}}"></script>

@section('content')
	<div id="app-sevicedesk">
    <announcement></announcement>
  </div>

  <script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop
