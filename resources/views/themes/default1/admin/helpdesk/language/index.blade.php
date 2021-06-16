@extends('themes.default1.admin.layout.admin')
<?php

        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.language-settings-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.language-settings-page-description') !!}">

@stop

@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('languages')
class="active"
@stop

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.settings') }}</h1>
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
            {{Session::get('success')}} @if(Session::has('link'))<a href="{{url(Session::get('link'))}}">{{Lang::get('lang.enable_lang')}}</a> @endif
        </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
<div class="box box-primary">
    <div class="box-header">
        <h2 class="box-title">{{ Lang::get('lang.language-settings') }}</h2><span class="pull-right"><a href="{{route('download')}}" title="click here to download template file" class="btn btn-primary"><i class="fa fa-download"></i> {{Lang::get('lang.download')}} </a> <a href="{{route('add-language')}}" class="btn btn-primary "><i class="fa fa-plus"></i> {{Lang::get('lang.add')}}</a></span>
    </div>
    <div class="box-body">
        <table class="table table-bordered" id="chumper">
            <thead>
                <tr>
                    <td>{!! Lang::get('lang.language') !!}</td>
                    <td>{!! Lang::get('lang.native_name') !!}</td>
                    <td>{!! Lang::get('lang.iso-code') !!}</td>
                    <td>{!! Lang::get('lang.system-default-lang') !!}</td>
                    <td>{!! Lang::get('lang.Action') !!}</td>
                </tr>
            </thead>
        </table>
    </div>
</div>
@stop
@push('scripts')
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
<script type="text/javascript">
    $('#chumper').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('getAllLanguages') !!}",
        "oLanguage": {
            "sLengthMenu": "_MENU_ Records per page",
            "sSearch"    : "Search: ",
            "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
        },
        columns:[
           {data:'language', name:'language'},
           {data:'native-name',name:'native-name'},
           {data: 'id', name: 'id'},
           {data: 'status', name: 'status'},
           {data: 'action', name: 'action'},
        ],
        "fnDrawCallback": function( oSettings ) {
            $("#tag-table").css({"opacity": "1"});
            $('.loader').css('display', 'none');
        },
        "fnPreDrawCallback": function(oSettings, json) {
            $('.loader').css('display', 'block');
            $("#tag-table").css({"opacity":"0.3"});
            $('#blur-bg').css({"opacity": "0.7", "z-index": "99999"});
        },
        "columnDefs": [
            { "orderable": false, "targets": []},
            { "searchable": false, "targets": [] },
        ]
    });
</script>
@endpush
