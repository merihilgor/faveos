@extends('themes.default1.agent.layout.agent')

@section('Staffs')
    active
@stop

@section('staffs-bar')
    active
@stop

@section('Report')
    class="active"
@stop
<style>

    .settingdivblue {
        width: 70px;
        height: 70px;
        margin: 0 auto;
        text-align: center;
        border: 5px solid #C4D8E4;
        border-radius: 100%;
        padding-top: 5px;
    }

    .settingiconblue p{
        text-align: center;
        font-size: 16px;
        word-wrap: break-word;
        font-variant: small-caps;
        font-weight: 500;
        line-height: 30px;
    }

</style>


@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
    <h1>{!!Lang::get('ServiceDesk::lang.servicedesk_reports')!!}</h1>
@stop
@section('content')
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{Lang::get('ServiceDesk::lang.asset')}}</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-2 col-sm-6">
                        <div class="settingiconblue">
                            <div class="settingdivblue">
                                <a href='{{ url("service-desk/reports/assets") }}'>
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-pie-chart fa-stack-1x"></i>
                                </span>
                                </a>
                            </div>
                            <p class="box-title" >{{Lang::get('ServiceDesk::lang.assets_reports')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
