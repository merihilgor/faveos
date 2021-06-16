@extends('themes.default1.agent.layout.agent')

@section('Users')
class="active"
@stop

@section('user-bar')
active
@stop

@section('user')
class="active"
@stop

<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('ServiceDesk::lang.export_assets')}}</h1>
@stop
<!-- /header -->
<!-- content -->
@section('content')
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
{!! Form::open(['url'=>'service-desk/assets/post/export','method'=>'post']) !!}
<div class="box box-primary">
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{Lang::get('ServiceDesk::lang.whoops')}}</strong>&nbsp;{{Lang::get('ServiceDesk::lang.there_were_some_problems_with_your_input')}} <br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(Session::has('success'))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{Session::get('success')}}
    </div>
    @endif
    <!-- fail message -->
    @if(Session::has('fails'))
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{Session::get('fails')}}
    </div>
    @endif
    <div class="box-header with-border">
        <h3 class="box-title">
            {{Lang::get('ServiceDesk::lang.assets')}}
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                    <span id="date"></span> <b class="caret"></b>
                </div>
            </div>
            <div class="col-md-6">
                {!! Form::hidden('date',null,['id'=>'hidden']) !!}
                {!! Form::submit(Lang::get('ServiceDesk::lang.export'),['class'=>'btn btn-success','id'=>'submit']) !!}
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}
@stop
@section('FooterInclude')
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript">
$(function () {

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('YYYY-MM-DD') + ' : ' + end.format('YYYY-MM-DD'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,

        locale: {
            "format": "MM/DD/YYYY",
            "separator": " - ",
            "applyLabel": "{{Lang::get('ServiceDesk::lang.apply')}}",
            "cancelLabel": "{{Lang::get('ServiceDesk::lang.cancel')}}",
            "fromLabel": "From",
            "toLabel": "To",
            "customRangeLabel": "{{Lang::get('ServiceDesk::lang.custom_range')}}",
           "firstDay": 1
        },
        ranges: {
            '{{Lang::get('ServiceDesk::lang.today')}}': [moment(), moment()],
            '{{Lang::get('ServiceDesk::lang.yesterday')}}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '{{Lang::get('ServiceDesk::lang.last_seven_days')}}': [moment().subtract(6, 'days'), moment()],
            '{{Lang::get('ServiceDesk::lang.last_thirty_days')}}': [moment().subtract(29, 'days'), moment()],
            '{{Lang::get('ServiceDesk::lang.this_month')}}': [moment().startOf('month'), moment().endOf('month')],
            '{{Lang::get('ServiceDesk::lang.last_month')}}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

});
$("#submit").on('click', function () {
    var date = $("#date").text();
    $("#hidden").val(date);
});


</script>
@stop