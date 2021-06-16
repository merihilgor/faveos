@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '1')->value('name');
$titleName = ($title) ? $title :"SUPPORT CENTER";
    ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.ticket-setting-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.ticket-setting-page-description') !!}">


@stop

@section('Tickets')
active
@stop

@section('tickets-bar')
active
@stop

@section('tickets')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{ Lang::get('lang.ticket') }}</h1>
@stop
<!-- /header -->
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop

@Section('custom-css')
<!-- /breadcrumbs -->
<style type="text/css">
      ul.tagit {
        width: 450%!important;
    }
    .source-counter{
        margin-left: 0px !important;
    }

      .box-header{
        padding-bottom:4px;
        background-color: #eee;

    }

    .box-container{
        margin: 10px 10px 20px 10px;
        border: 1px solid #ddd;
        background-color: white;
         /*box-shadow: 5px 10px #888888;*/
    }

</style>
@stop
<!-- /breadcrumbs -->
<!-- content -->
@section('content')
<!-- open a form -->
{!! Form::model($tickets,['url' => 'postticket/'.$tickets->id, 'method' => 'PATCH','id'=>'Form']) !!}
 <!-- check whether success or not -->
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fa  fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!! Session::get('success') !!}
        </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!! Session::get('fails') !!}
        </div>
        @endif
        @if(Session::has('errors'))
        <?php //dd($errors);?>
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <br/>
            @if($errors->first('status'))
            <li class="error-message-padding">{!! $errors->first('status', ':message') !!}</li>
            @endif
            @if($errors->first('priority'))
            <li class="error-message-padding">{!! $errors->first('priority', ':message') !!}</li>
            @endif
            @if($errors->first('sla'))
            <li class="error-message-padding">{!! $errors->first('sla', ':message') !!}</li>
            @endif
            @if($errors->first('help_topic'))
            <li class="error-message-padding">{!! $errors->first('help_topic', ':message') !!}</li>
            @endif
            @if($errors->first('collision_avoid'))
            <li class="error-message-padding">{!! $errors->first('collision_avoid', ':message') !!}</li>
            @endif
            @if($errors->first('ticket_number_prefix'))
                <li class="error-message-padding">{!! $errors->first('ticket_number_prefix', ':message') !!}</li>
            @endif
        </div>
        @endif

<div class="box box-primary">
    <div class="box-header with-border hide" style="background: white;">
        <h3 class="box-title">{{Lang::get('lang.ticket-setting')}}</h3>
    </div>
    <div class="box-body">


<div class="box-container">
    <div class="box-header with-border">
        <h3 class="box-title">{{Lang::get('lang.general_settings')}}</h3>
    </div>
    <div class="box-body">



        <div class="row">
            <!-- Default Status: Required : manual: Dropdowm  -->
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                    {!! Form::label('status',Lang::get('lang.default_status')) !!}
                    {!!Form::select('status',[Lang::get('lang.select_status') => $status->where('purpose_of_status', 1)->pluck('name','id')->toArray()],$tickets->status,['class' => 'form-control select']) !!}
                </div>
            </div>

             <div class="col-md-4">
                <div class="form-group {{ $errors->has('collision_avoid') ? 'has-error' : '' }}">
                    {!! Form::label('collision_avoid',Lang::get('lang.agent_collision_avoidance_duration')) !!}
                    <div class="input-group">
                        <input type="text" class="form-control numberOnly" name="collision_avoid" min="0"  step="1" value="{{$tickets->collision_avoid}}" placeholder="in minutes">
                        <div class="input-group-addon">
                            <span><i class="fa fa-clock-o"></i> {!!Lang::get('lang.in_minutes')!!}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('help_topic') ? 'has-error' : '' }}">
                    {!! Form::label('help_topic',Lang::get('lang.lock_ticket_frequency')) !!}

                    <select name='lock_ticket_frequency' class="form-control">
                        <option @if($tickets->lock_ticket_frequency == null) selected="true" @endif value="0">{!! Lang::get('lang.no')!!}</option>
                        <option @if($tickets->lock_ticket_frequency == 1) selected="true" @endif value="1">{!! Lang::get('lang.only-once')!!}</option>
                        <option @if($tickets->lock_ticket_frequency == 2) selected="true" @endif value="2">{!! Lang::get('lang.frequently')!!}</option>
                    </select>

                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('ticket_number_prefix') ? 'has-error' : '' }}">
                    {!! Form::label('ticket_number_prefix',Lang::get('lang.ticket_number_prefix')) !!}
                    <a href="#" data-toggle="tooltip" data-placement="right" title="{{Lang::get('lang.ticket_number_prefix_description')}}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                    {!! Form::text('ticket_number_prefix',null,['class'=>'form-control','id'=>'ticket-number-prefix', 'minlength'=>3, 'maxlength'=>4,'required'=>1]) !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('record_per_page') ? 'has-error' : '' }}">
                    {!! Form::label('record_per_page', Lang::get('lang.show_ticket_per_page')) !!}
                    <a href="#" data-toggle="tooltip" data-placement="right" title="{{Lang::get('lang.ticket-perpage-tooltip')}}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>
                    <?php $ticlet_per_page = (Cache::has('ticket_per_page')) ? Cache::get('ticket_per_page') : null; ?>
                    {!! Form::select('record_per_page',['10'=> Lang::get('lang.ticket_per_page', ['value' => 10]),'25'=>Lang::get('lang.ticket_per_page', ['value' => 25]),'50'=>Lang::get('lang.ticket_per_page', ['value' => 50]),'100'=>Lang::get('lang.ticket_per_page', ['value' => 100])],$ticlet_per_page,['class'=>'form-control','id'=>'type']) !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('waiting_time') ? 'has-error' : '' }}">
                    {!! Form::label('waiting_time',Lang::get('lang.waiting_time')) !!}
                    <a href="#" data-toggle="tooltip" data-placement="right" title="{{Lang::get('lang.waiting_time_for_client_reply')}}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a>

                    <div class="input-group">
                        {!! Form::number('waiting_time', null, ['class'=>'form-control','min'=>0,'max'=>'17520']) !!}
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i> {!!Lang::get('lang.in_hours')!!}</span>
                    </div>
                </div>
            </div>
        </div>
      </div>
  </div>

<div class="box-container new-inbox">
     <div class="box-header with-border">

            <h3 class="box-title">{{Lang::get('lang.inbox_settings')}}</h3>

    </div>
     <div class="box-body">
        <div class="row new-inbox">

       {{-- Count Internal notes in thread count --}}
            <div class="col-md-4">
                <div class="form-group">

                    {!! Form::label('include_internal_note_in_thread_count', Lang::get('lang.include_internal_note_in_thread_count')) !!}

                    <br>
                    {!! Form::radio('count-internal', '1', $tickets->count_internal) !!}
                    &nbsp;&nbsp;&nbsp;{!! Lang::get('lang.yes') !!}&nbsp;&nbsp;&nbsp;&nbsp;

                    {!! Form::radio('count-internal', '0', !$tickets->count_internal) !!}
                    &nbsp;{!! Lang::get('lang.no') !!}
                </div>
            </div>
            {{-- =======================Till here ========================== --}}


            {{-- show inbox status update date in inbox --}}
            <div class="col-md-4">
                <div class="form-group">

                    {!! Form::label('show_status_update_date', Lang::get('lang.show_status_update_date')) !!}

                    <br>
                    {!! Form::radio('show-status-date', '1', $tickets->show_status_date) !!}
                    &nbsp;&nbsp;&nbsp;{!! Lang::get('lang.yes') !!}&nbsp;&nbsp;&nbsp;&nbsp;
                    {!! Form::radio('show-status-date', '0', !$tickets->show_status_date) !!}
                    &nbsp;{!! Lang::get('lang.no') !!}
                </div>
            </div>
            {{-- =======================Till here ========================== --}}


            {{-- show organisation details in inbox --}}
            <div class="col-md-4">
                <div class="form-group">

                    {!! Form::label('show_org_details', Lang::get('lang.show_org_details')) !!}

                    <br>
                    {!! Form::radio('show-org-details', '1', $tickets->show_org_details) !!}
                    &nbsp;&nbsp;&nbsp;{!! Lang::get('lang.yes') !!}&nbsp;&nbsp;&nbsp;&nbsp;

                    {!! Form::radio('show-org-details', '0', !$tickets->show_org_details) !!}
                    &nbsp;{!! Lang::get('lang.no') !!}
                </div>
            </div>
            {{-- =======================Till here ========================== --}}



        </div>

             <div class="row  custom-label">
                 <div class="col-md-12" >
                    {!! Form::label('add_custom_field', Lang::get('lang.add_custom_fields')) !!}
                   <div class="custom-ticket-policy" id="custom-field">
                   </div>
                 </div>
             </div>
        </div>
   </div>
</div>
 <div class="box-footer">
 <button type="submit" class="btn btn-primary" id="submit-button" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> Saving..."><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}</button>

    </div>
</div>

{!! Form::close() !!}
@stop
@section('FooterInclude')


<script type="text/javascript">

   var custom ='{!! json_encode($customTicketEnforcements) !!}';

  $.ajax({
                url: "{{ url('api/ticket-custom-fields-flattened') }}",
                dataType: "json",
                type: "GET",
                success: function(response){
                    response = response.data;
                    if(response.length == 0){
                        $('.custom-label').hide();
                    }
                    else{
                        $('.custom-label').show();
                    }

               for(i in response) {
                    if(custom.indexOf(response[i].id) != -1){

                       $html="<input type='checkbox' name="+response[i].id+" value="+response[i].id+" id="+response[i].id+" checked> "+response[i].label+"<br/>";
                    }
                    else{
                        $html="<input type='checkbox' name="+response[i].id+" value="+response[i].id+" id="+response[i].id+"> "+response[i].label+"<br/>";
                    }

                      $('.custom-ticket-policy').append($html);
                   }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });

</script>
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
});

</script>
@stop
