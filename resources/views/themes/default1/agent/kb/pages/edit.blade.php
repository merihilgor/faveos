@extends('themes.default1.agent.layout.agent')
@extends('themes.default1.agent.layout.sidebar')    

@section('pages')
active
@stop
@section('all-pages')
class="active"
@stop

<style type="text/css">
	.cke_chrome {
     display: none !important;  
	}
</style>

@section('PageHeader')
<h1>{{Lang::get('lang.pages')}}</h1>
@stop

@section('content')

<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>

<div id="app-agent">
        <pages></pages>
    </div>
    <script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop
