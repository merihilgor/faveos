@extends('themes.default1.agent.layout.agent')
<style>
    .left-hand{
        float: left;
    }

  .table{
        overflow-x: auto;
    }

     #prob_desc table{
        overflow-x: auto;
     }

     #prob_desc table, #prob_desc table td, #prob_desc table tr, #prob_desc table th {  
      border: 1px solid #ddd;
      text-align: left;
    }

     #prob_desc table {
      border-collapse: collapse;
      width: max-content;
    }

     #prob_desc table th,  #prob_desc table td {
      padding: 15px;
    }
</style>
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        ?>
@section('meta-tags')
<meta name="title" content="{{Lang::get('ServiceDesk::lang.problems_view-page-title')}}  :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{{Lang::get('ServiceDesk::lang.problems_view-page-description')}} ">

@stop

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop

@section('PageHeader')
    <h1> {{Lang::get('ServiceDesk::lang.problem')}} </h1>
@stop

<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>

@section('content')
    
    <div id="app-sevicedesk">
    
        <problem-view></problem-view>
    </div>

    <script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop
