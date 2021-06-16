@extends('themes.default1.agent.layout.agent')

@extends('themes.default1.agent.layout.sidebar')    

@section('pages')
active
@stop

<style type="text/css">
	.cke_chrome {
     display: none !important;  
	}
</style>

@section('add-pages')
class="active"
@stop

@section('PageHeader')
<h1>{{Lang::get('lang.pages')}}</h1>
@stop

<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>

@section('content')
    <div id="app-agent">
        <pages></pages>
    </div>
    <script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop
