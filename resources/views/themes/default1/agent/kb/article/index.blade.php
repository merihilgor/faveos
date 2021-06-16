@extends('themes.default1.agent.layout.agent')
@extends('themes.default1.agent.layout.sidebar')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.article_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.article_lists-page-description') !!}">
@stop


@section('custom-css')
<link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet"  type="text/css"/>
@stop

@section('article')
active
@stop

@section('all-article')
class="active"
@stop

@section('PageHeader')
<h1>{{Lang::get('lang.article')}}</h1>
@stop
<style>
    table { table-layout:fixed; word-break:break-all; word-wrap:break-word; }
</style>


@section('content')
<div class="box box-primary">

    <div class="box-header with-border">
        <h2 class="box-title">{{Lang::get('lang.list_of_articles')}}</h2>
        <a href="{{url('article/create')}}" class="btn btn-primary pull-right"><span class='glyphicon glyphicon-plus'></span> &nbsp;{{Lang::get('lang.create_article')}}</a>

    </div>
    <div class="box-body">

        <div id="app-agent">
            <articles-list></articles-list>
        </div>
    </div>
</div>
<script src="{{asset('js/agent.js')}}" type="text/javascript"></script>
@stop
