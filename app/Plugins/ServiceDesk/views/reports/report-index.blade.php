@extends('themes.default1.agent.layout.agent')
<?php
    $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
    $titleName = $title->name ?? "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{{Lang::get('ServiceDesk::lang.reports-assets-title')}}  :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{{Lang::get('ServiceDesk::lang.reports-assets-description')}} ">
@stop

@section('PageHeader')
    <h1> {{Lang::get('ServiceDesk::lang.servicedesk_reports')}} </h1>
@stop

@section('content')
    
    <div id="app-sevicedesk">
        <report-index></report-index>
    </div>

    <script src="{{ asset('js/serviceDesk.js') }}"></script> 
@stop
