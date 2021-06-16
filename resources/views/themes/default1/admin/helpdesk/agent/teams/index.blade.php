@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.teams_lists-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.teams_lists-page-description') !!}">
@stop

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop

@section('Staffs')
active
@stop

@section('staffs-bar')
active
@stop

@section('teams')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.teams')}}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
 <!-- check whether success or not -->
                @if(Session::has('success'))
                <div class="alert alert-success alert-dismissable">
                    <i class="fa  fa-check-circle"></i>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{Session::get('success')}}
                </div>
                @endif
                <!-- failure message -->
                @if(Session::has('fails'))
                <div class="alert alert-danger alert-dismissable">
                    <i class="fa fa-ban"></i>
                    <b>Fail!</b>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{Session::get('fails')}}
                </div>
                @endif
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h2 class="box-title">{{Lang::get('lang.list_of_teams')}}</h2><a href="{{route('teams.create')}}" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;{{Lang::get('lang.create_team')}}</a></div>
            <div class="box-body">
               <table id="chumper" class="table table-bordered dataTable no-footer">
                <thead>
                    <tr>
                        <td>{!! Lang::get('lang.name') !!}</td>
                        <td>{!! Lang::get('lang.status') !!}</td>
                        <td>{!! Lang::get('lang.team_members') !!}</td>
                        <td>{!! Lang::get('lang.team_lead') !!}</td>
                        <td>{!! Lang::get('lang.action') !!}</td>
                    </tr>
            </table>
            </div>
        </div>
    </div>
</div>
@stop
@push('scripts')
@include('vendor.yajra.team-table')
 <script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
 <script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>

@endpush