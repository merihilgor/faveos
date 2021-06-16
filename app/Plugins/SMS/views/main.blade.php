@extends('themes.default1.admin.layout.admin')

@section('Plugins')
active
@stop

@section('settings-bar')
active
@stop

@section('plugin')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.plugins') !!}</h1>
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
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{{Lang::get('SMS::lang.SMS')}}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ route('providers-settings') }}">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-wrench fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('SMS::lang.provider-settings')}}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
                <!--/.col-md-2-->
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ route('sms-template-sets') }}">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-file-text fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('SMS::lang.sms-templates')}}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>
<!-- /.box -->

@stop

@section('FooterInclude')

@stop

<!-- /content -->
