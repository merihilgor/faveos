@extends('themes.default1.agent.layout.agent')
<?php
    $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
    $lang = 'ServiceDesk::lang.'.$reportType.'_report'; 
    $titleName = $title->name ?? "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{{Lang::get('ServiceDesk::lang.reports-assets-edit-title')}}  :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{{Lang::get('ServiceDesk::lang.reports-assets-edit-description')}} ">
@stop

@section('PageHeader')
    <h1> {{ Lang::get($lang) }} </h1>
@stop

@section('content')
  
   <div id="app-sevicedesk">
        <report-nested-filter></report-nested-filter>
    </div>

    <script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop
