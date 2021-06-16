@extends('themes.default1.admin.layout.admin')

<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (isset($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        
        ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.api_settings-page-title') !!} :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{!! Lang::get('lang.api_settings-page-description') !!}">

@stop

@section('Settings')
active
@stop

@section('api-set')
class=active
@stop

@section('settings-bar')
active
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.api')}}</h1>
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
<div class="box box-primary">
    {!! Form::open(['url'=>'api','method'=>'post','files'=>true,'id'=>'Form']) !!}
    <div class="box-header with-border">
        <h3 class="box-title">{{Lang::get('lang.api_settings')}}</h3>     

    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                
                <h4>{!! Lang::get('lang.api_configurations') !!}</h4>
                <!-- Guest user page Content -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('api_enable') ? 'has-error' : '' }}">
                            {!! Form::label('api',Lang::get('lang.api')) !!}
                            {!! $errors->first('api_enable', '<spam class="help-block">:message</spam>') !!}
                            <div class="row">
                                <div class="col-xs-5">
                                    <input type="radio" name="api_enable" value="1" @if($systems->api_enable ==1) checked @endif>&nbsp;{{Lang::get('lang.enable')}}
                                           <!-- {!! Form::radio('api_enable','1',true) !!} {{Lang::get('lang.enable')}} -->
                                </div>
                                <div class="col-xs-5">
                                    <input type="radio" name="api_enable" value="0" @if($systems->api_enable == 0) checked @endif>&nbsp;{{Lang::get('lang.disable')}}
                                           <!-- {!! Form::radio('api_enable','0') !!} {{Lang::get('lang.disable')}} -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('api_key_mandatory') ? 'has-error' : '' }}">
                            {!! Form::label('api_key_mandatory',Lang::get('lang.api_key_mandatory')) !!}
                            {!! $errors->first('api_key_mandatory', '<spam class="help-block">:message</spam>') !!}
                            <div class="row">
                                <div class="col-xs-5">
                                    <input type="radio" name="api_key_mandatory" value="1" @if($systems->api_key_mandatory == 1) checked @endif>&nbsp;{{Lang::get('lang.enable')}}
                                           <!-- {!! Form::radio('api_key_mandatory','1',true) !!} {{Lang::get('lang.enable')}} -->
                                </div>
                                <div class="col-xs-5">
                                    <input type="radio" name="api_key_mandatory" value="0" @if($systems->api_key_mandatory == 0) checked @endif>&nbsp;{{Lang::get('lang.disable')}}
                                           <!-- {!! Form::radio('api_key_mandatory','0') !!} {{Lang::get('lang.disable')}} -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Date and Time Format: text: required: eg - 03/25/2015 7:14 am -->
                    <div class="col-md-4">
                     {!! Form::label('api_key',Lang::get('lang.api_key')) !!}
                        <div class="form-group {{ $errors->has('api_key') ? 'has-error' : '' }} input-group">
                            {!! $errors->first('api_key', '<spam class="help-block">:message</spam>') !!}
                            {!! Form::text('api_key',$systems->api_key,['class' => 'col-auto form-control','id'=>'api_key']) !!}                           
                            <div type="button" class="btn btn-default input-group-addon" id="visible-button" value="Copy"><i class="fa fa-clipboard" aria-hidden="true"></i></div>
                        </div>
                        <a class="btn btn-default "  id="generate"> <i class="fa fa-refresh"> </i> {!! Lang::get('lang.generate_key') !!}</a>
                    </div>  
                </div>
            </div>
        </div>
    </div>
    
    <div class="box-footer">
<!--        {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}-->
            <button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> {!! Lang::get('lang.saving') !!}"><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
    
    </div>
    {!! Form::close() !!}   
</div>
<a href="#" id="" data-toggle="modal" data-target="#generateModal"></a>    
<div class="modal fade" id="generateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{!! Lang::get('lang.api_key') !!}</h4>
            </div>
            <div class="modal-body" id="messageBody">
            </div>
            <div class="modal-footer" id="messageBody">
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">{!! Lang::get('lang.close') !!}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script src="{{assetLink('js','ajax-jquery')}}"></script>
<script type="text/javascript">
jQuery(document).ready(function () {
// Close a ticket
    $('#generate').on('click', function (e) {
        $.ajax({
            type: "GET",
            url: "{!! url('generate-api-key') !!}",
            beforeSend: function () {
//                $('.loader1').css('display','block');
//                $("#generate").empty();
//                var message = "<i class='fa fa-refresh fa-spin'> </i>  <?php echo Lang::get('lang.generate_key'); ?>";
//                $('#generate').html(message);
            },
            success: function (response) {
                // alert(response);
//                $('.loader1').css('display','none');
//                $("#messageBody").empty();
//                var message = "<div class='alert alert-default' style='margin-bottom: -5px;'>Copy and paste in the api to set a different Api key</div> <br/><b>Api key</b> : " + response;
                $('#api_key').val(response);

                $('#clickGenerate').trigger("click");
                $("#generate").empty();
                var message = "<i class='fa fa-refresh'> </i>  <?php echo Lang::get('lang.generate_key'); ?>";
                $('#generate').html(message);
            }
        })
        return false;
    });
});
</script>
<script>
   // Copy to clipboard example
document.querySelector("#visible-button").onclick = function() {
  // Select the content
  document.querySelector("#api_key").select();
  // Copy to the clipboard
  document.execCommand('copy');
};


</script>
    
@stop
