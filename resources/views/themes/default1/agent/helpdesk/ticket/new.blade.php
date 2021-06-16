@extends('themes.default1.agent.layout.agent')
<?php
$title     = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title : "SUPPORT CENTER";
?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.create_ticket-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.create_ticket-page-description') !!}">
@stop


@section('Tickets')
active
@stop

@section('newticket')
class="active"
@stop

@section('custom-css')

<link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';" />
<link href="{{assetLink('css','intlTelInput')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';"/>

<style>
   .btn-bs-file{
    position:relative;
}
.btn-bs-file input[type="file"]{
    position: absolute;
    filter: alpha(opacity=0);
    opacity: 0;
    width:0;
    height:0;
    outline: none;
    cursor: inherit;
}
.danis:hover{
    background-color: #5897fb;
}
.danis:hover>div>a{
    color:white !important;

}
</style>
@stop
@section('Tickets')
class="active"
@stop

@section('ticket-bar')
active
@stop

@section('newticket')
class="active"
@stop

@section('PageHeader')
<h1 id="header">{{Lang::get('lang.tickets')}}</h1>
@stop

<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>

@section('content')

@include('themes.default1.common.recaptcha')

<div id="app-agent">
  <ticket-form :panel="'agent'"></ticket-form>
</div>

<script src="{{asset('js/agent.js')}}" type="text/javascript"></script>

@stop
@push('scripts')
<script src="{{assetLink('js','ckeditor')}}"></script>
<script src="{{assetLink('js','intlTelInput')}}" async></script>
<script src="{{assetLink('js','select2')}}" type="text/javascript"></script>
@endpush