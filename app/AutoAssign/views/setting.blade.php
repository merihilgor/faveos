@extends('themes.default1.admin.layout.admin')

@section('Tickets')
active
@stop

@section('tickets-bar')
active
@stop

@section('auto-assign')
class="active"
@stop

@section('HeadInclude')
@stop

@section('custom-css')
 <link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
@stop

@section('PageHeader')
<h1>{{ Lang::get('lang.auto_assign') }}</h1>
@stop

@section('content')
  @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- fail message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{{Lang::get('message.alert')}}!</b> {{Lang::get('message.failed')}}.
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
<div class="box box-primary">

        
        {!! Form::open(['url'=>'auto-assign','method'=>'post','id'=>'Form']) !!}

    <!-- /.box-header -->
    <div class="box-body">
      
        <div class="row">
            <div class="form-group col-md-6 {{ $errors->has('status') ? 'has-error' : '' }}">
                <div>
                    {!! Form::label('status',Lang::get('lang.enable')) !!}
                </div>
                <div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('status',1,isAutoAssign()) !!} {!! Lang::get('lang.yes') !!}</p>
                    </div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('status',0,!isAutoAssign()) !!} {!!Lang::get('lang.no')!!}</p>
                    </div>
                </div>             
            </div>
            <div class="form-group col-md-6 {{ $errors->has('only_login') ? 'has-error' : '' }}" >
                <div>
                    {!! Form::label('only_login',Lang::get('lang.only-login-agents')) !!}
                </div>
                <div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('only_login',1,isOnlyLogin()) !!} {!! Lang::get('lang.yes') !!}</p>
                    </div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('only_login',0,!isOnlyLogin()) !!} {!! Lang::get('lang.no') !!}</p>
                    </div>
                    
                </div>             
            </div>
            
             <div class="form-group col-md-6 {{ $errors->has('assign_not_accept') ? 'has-error' : '' }}">
                <div>
                    {!! Form::label('assign_not_accept',Lang::get('lang.assign-ticket-even-agent-in-non-acceptable-mode')) !!}
                </div>
                <div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('assign_not_accept',1,isAssignIfNotAccept()) !!} {!! Lang::get('lang.yes') !!}</p>
                    </div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('assign_not_accept',0,!isAssignIfNotAccept()) !!} {!! Lang::get('lang.no') !!}</p>
                    </div>
                    
                </div>             
            </div>
            
            <div class="form-group col-md-6 {{ $errors->has('assign_with_type') ? 'has-error' : '' }}">
                <div>
                    {!! Form::label('assign_with_type',Lang::get('lang.assign-ticket-with-agent-having-type')) !!}
                </div>
                <div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('assign_with_type',1,isAssignWithType()) !!} {!! Lang::get('lang.yes') !!}</p>
                    </div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('assign_with_type',0,!isAssignWithType()) !!} {!! Lang::get('lang.no') !!}</p>
                    </div>
                    
                </div>             
            </div>
            
            
            
            
             <div class="form-group col-md-6 {{ $errors->has('is_location') ? 'has-error' : '' }}">
                <div>
                    {!! Form::label('is_location',Lang::get('lang.assign-ticket-with-agent-having-location')) !!}
                </div>
                <div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('is_location',1,isAssignWithLocation()) !!} {!! Lang::get('lang.yes') !!}</p>
                    </div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('is_location',0,!isAssignWithLocation()) !!} {!! Lang::get('lang.no') !!}</p>
                    </div>
                    
                </div>             
            </div>
            <div class="col-md-6">
                <div>
                    {!! Form::label('assign_department_option', Lang::get('lang.auto-assign-enabled-departments')) !!}
                </div>
                <div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('assign_department_option', 'all', deptAssignOption()=='all', ['onclick' => 'updateDepartmentList("all")']) !!} {!! Lang::get('lang.all') !!}</p>
                    </div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('assign_department_option', 'specific', deptAssignOption()=='specific', ['onclick' => 'updateDepartmentList("specific")']) !!} {!! Lang::get('lang.specific') !!}</p>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6 {{ $errors->has('thresold') ? 'has-error' : '' }}">
                {!! Form::label('thresold',Lang::get('lang.maximum-number-of-ticket-can-assign-to-agent')) !!}
                {!! Form::input('text', 'thresold',thresold(),['class'=>'form-control numberOnly','placeholder'=>'100', 'min' => 0]) !!}             
            </div>
            <div class="col-md-6" id="department-list">
                {!! Form::label('department_list',Lang::get('lang.select-deparment')) !!}
                {!! Form::select('department_list[]', [Lang::get('lang.departments')=>$departments->pluck('name','id')->toArray()],$dept,['multiple'=>true,'class'=>"form-control select2" ,'id'=>"primary_department",'required','style'=>"width:100%"]) !!}
            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <div class="box-footer">
     <!--  {!! Form::submit(Lang::get('lang.save'),['class'=>'btn btn-primary']) !!} -->
    <button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i>{!! Lang::get('lang.saving') !!}"><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
     
        {!! Form::close() !!}
    </div>
    <!-- /.box -->
</div>
<script src="{{assetLink('js','select2')}}"></script>
<script>
$(document).ready(function() {
    $(".numberOnly").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl/cmd+A
            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: Ctrl/cmd+C
            (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: Ctrl/cmd+X
            (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    updateDepartmentList("{!! Input::old('assign_department_option', deptAssignOption()) !!}");
});

function updateDepartmentList($option)
{
    $('#department-list').css('display', 'none');
    $('#primary_department').prop("disabled", true);
    if ($option != 'all') {
        $('#department-list').css('display', 'block');
        $('#primary_department').prop("disabled", false);
    }
}

$('#primary_department').select2({
    placeholder: "{{Lang::get('lang.Choose_departments...')}}",
    minimumInputLength: 2,
    ajax: {
        url: '{{url("api/dependency/departments")}}',
        dataType: 'json',
        beforeSend: function(){
            $('.loader').css('display', 'block');
        },
        complete: function() {
            $('.loader').css('display', 'none');
        },
        data: function (params) {
            return {
                "search-query": $.trim(params.term)
            };
        },
        processResults: function (data) {
            let res  = [];
            data.data.departments.forEach(function(item) {
                res.push({"id":item.id,"text":item.name});
            });
            return {
                results: res
            };
        },
        cache: true
    }
});
</script>
@stop