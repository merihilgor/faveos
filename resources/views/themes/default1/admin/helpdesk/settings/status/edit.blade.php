@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.status_edit-page-title') !!} :: {!! strip_tags($title_name) !!} ">

<meta name="description" content="{!! Lang::get('lang.status_edit-page-description') !!}">


@stop

@section('Tickets')
active
@stop

@section('status')
class="active"
@stop

@section('PageHeader')
<h1>{!! Lang::get('lang.status_settings') !!}</h1>
@stop

@section('custom-css')
<style type="text/css">
    .select2-container--default .select2-selection--single {
    border-radius: 0px !important;
    height: 33px !important;
}
.select2-container .select2-selection--single .select2-selection__rendered {
    padding-top: 9px;
}
#ticket-status-icon-container .select2.select2-container.select2-container--default{
  width: 100px !important;
}
#ticket-status-icon-container .select2-container--default .select2-selection--single .select2-selection__rendered {
  padding-top: 2px !important;
}
</style>
@stop

@section('content')


<link href="{{assetLink('css','bootstrap-colorpicker')}}" rel="stylesheet">
<!--select 2-->
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
{!! Form::model($status,['route'=>['statuss.update', $status->id],'method'=>'PATCH','files' => true,'id'=>'Form']) !!}
 @if(Session::has('errors'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <br/>
            @foreach ($errors->all() as $error)
            <li class="error-message-padding">{{ $error }}</li>
            @endforeach
        </div>
        @endif
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fa fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!! Session::get('success') !!}
        </div>
        @endif
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!} !</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p>{!! Session::get('fails') !!}</p>
        </div>
        @endif
<div class="box box-primary">
    <div class="box-header with-border">
        <h4 class="box-title">{!! Lang::get('lang.edit_status') !!}</h4>
    </div><!-- /.box-header -->
    <div class="box-body">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.name') !!}: <span class="text-red"> *</span></label><br>
                    {!! Form::text('name',null,['class'=>'form-control'])!!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('sort') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.display_order') !!}: <span class="text-red"> *</span></label><br>
                    <input type="number" name="sort" min="1" min="1" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="form-control" value="{!! $status->order !!}" required>
                </div>
            </div>
            <div class="col-md-2"  id="ticket-status-icon-container">
                <div class="f'orm-group {{ $errors->has('icon_class') ? 'has-error' : '' }}">
                    <i class=></i>
                    <label>{!! Lang::get('lang.icon_class') !!}: <span class="text-red"> *</span></label><br>
                    <select class="form-control icons"  name="icon_class" style="font-family: FontAwesome, sans-serif;" required>
                        <option <?php if ($status->icon == "fa fa fa-edit") echo 'selected="selected"' ?> value="fa fa-edit">&#xf044</option>
                        <option <?php if ($status->icon == "fa fa-folder-open") echo 'selected="selected"' ?> value="fa fa-folder-open">&#xf07c</option>
                        <option <?php if ($status->icon == "fa fa-minus-circle") echo 'selected="selected"' ?> value="fa fa-minus-circle">&#xe082</option>
                        <option <?php if ($status->icon == "glyphicon glyphicon-alert") echo 'selected="selected"' ?> value="glyphicon glyphicon-alert">&#xe209</option>
                        <option <?php if ($status->icon == "fa fa fa-bars") echo 'selected="selected"' ?> value="fa fa-bars">&#xf0c9</option>
                        <option <?php if ($status->icon == "fa fa-bell-o") echo 'selected="selected"' ?> value="fa fa-bell-o">&#xf0a2</option>
                        <option <?php if ($status->icon == "fa fa-bookmark-o") echo 'selected="selected"' ?> value="fa fa-bookmark-o">&#xf097</option>
                        <option <?php if ($status->icon == "fa fa-bug") echo 'selected="selected"' ?> value="fa fa-bug">&#xf188</option>
                        <option <?php if ($status->icon == "fa fa-bullhorn") echo 'selected="selected"' ?> value="fa fa-bullhorn">&#xf0a1</option>
                        <option <?php if ($status->icon == "fa fa-calendar") echo 'selected="selected"' ?> value="fa fa-calendar">&#xf073</option>
                        <option <?php if ($status->icon == "fa fa-cart-plus") echo 'selected="selected"' ?> value="fa fa-cart-plus">&#xf217</option>
                        <option <?php if ($status->icon == "fa fa-check") echo 'selected="selected"' ?> value="fa fa-check">&#xf00c</option>
                        <option <?php if ($status->icon == "fa fa-check-circle") echo 'selected="selected"' ?> value="fa fa-check-circle">&#xf058</option>
                        <option <?php if ($status->icon == "fa fa-check-circle-o") echo 'selected="selected"' ?> value="fa fa-check-circle-o">&#xf05d</option>
                        <option <?php if ($status->icon == "fa fa-check-square") echo 'selected="selected"' ?> value="fa fa-check-square">&#xf14a</option>
                        <option <?php if ($status->icon == "fa fa-check-square-o") echo 'selected="selected"' ?> value="fa fa-check-square-o">&#xf046</option>
                        <option <?php if ($status->icon == "fa fa-circle-o-notch") echo 'selected="selected"' ?> value="fa fa-circle-o-notch">&#xf1ce</option>
                        <option <?php if ($status->icon == "fa fa-clock-o") echo 'selected="selected"' ?> value="fa fa-clock-o">&#xf017</option>
                        <option <?php if ($status->icon == "fa fa-close") echo 'selected="selected"' ?> value="fa fa-close">&#xf00d</option>
                        <option <?php if ($status->icon == "fa fa-code") echo 'selected="selected"' ?> value="fa fa-code">&#xf121</option>
                        <option <?php if ($status->icon == "fa fa-hand-paper-o") echo 'selected="selected"' ?> value="fa fa-hand-paper-o">&#xf256</option>
                        <option <?php if ($status->icon == "fa fa-hourglass-half") echo 'selected="selected"' ?> value="fa fa-hourglass-half">&#xf252</option>
                        <option <?php if ($status->icon == "fa fa-cog") echo 'selected="selected"' ?> value="fa fa-cog">&#xf013</option>
                        <option <?php if ($status->icon == "fa fa-cogs") echo 'selected="selected"' ?> value="fa fa-cogs">&#xf085</option>
                        <option <?php if ($status->icon == "fa fa-comment") echo 'selected="selected"' ?> value="fa fa-comment">&#xf075</option>
                        <option <?php if ($status->icon == "fa fa-comment-o") echo 'selected="selected"' ?> value="fa fa-comment-o">&#xf0e5</option>
                        <option <?php if ($status->icon == "fa fa-commenting") echo 'selected="selected"' ?> value="fa fa-commenting">&#xf27a</option>
                        <option <?php if ($status->icon == "fa fa-commenting-o") echo 'selected="selected"' ?> value="fa fa-commenting-o">&#xf27b</option>
                        <option <?php if ($status->icon == "fa fa-comments") echo 'selected="selected"' ?> value="fa fa-comments">&#xf086</option>
                        <option <?php if ($status->icon == "fa fa-comments-o") echo 'selected="selected"' ?> value="fa fa-comments-o">&#xf0e6</option>
                        <option <?php if ($status->icon == "fa fa-edit") echo 'selected="selected"' ?> value="fa fa-edit">&#xf044</option>
                        <option <?php if ($status->icon == "fa fa-envelope-o") echo 'selected="selected"' ?> value="fa fa-envelope-o">&#xf003</option>
                        <option <?php if ($status->icon == "fa fa-exchange") echo 'selected="selected"' ?> value="fa fa-exchange">&#xf0ec</option>
                        <option <?php if ($status->icon == "fa fa-exclamation") echo 'selected="selected"' ?> value="fa fa-exclamation">&#xf12a</option>
                        <option <?php if ($status->icon == "fa fa-exclamation-triangle") echo 'selected="selected"' ?> value="fa fa-exclamation-triangle">&#xf071</option>
                        <option <?php if ($status->icon == "fa fa-external-link") echo 'selected="selected"' ?> value="fa fa-external-link">&#xf08e</option>
                        <option <?php if ($status->icon == "fa fa-eye") echo 'selected="selected"' ?> value="fa fa-eye">&#xf06e</option>
                        <option <?php if ($status->icon == "fa fa-feed") echo 'selected="selected"' ?> value="fa fa-feed">&#xf09e</option>
                        <option <?php if ($status->icon == "fa fa-flag-o") echo 'selected="selected"' ?> value="fa fa-flag-o">&#xf11d</option>
                        <option <?php if ($status->icon == "fa fa-flash") echo 'selected="selected"' ?> value="fa fa-flash">&#xf0e7</option>
                        <option <?php if ($status->icon == "fa fa-folder-o") echo 'selected="selected"' ?> value="fa fa-folder-o">&#xf114</option>
                        <option <?php if ($status->icon == "fa fa-folder-open-o") echo 'selected="selected"' ?> value="fa fa-folder-open-o">&#xf115</option>
                        <option <?php if ($status->icon == "fa fa-group") echo 'selected="selected"' ?> value="fa fa-group">&#xf0c0</option>
                        <option <?php if ($status->icon == "fa fa-info") echo 'selected="selected"' ?> value="fa fa-info">&#xf129</option>
                        <option <?php if ($status->icon == "fa fa-life-ring") echo 'selected="selected"' ?> value="fa fa-life-ring">&#xf1cd</option>
                        <option <?php if ($status->icon == "fa fa-line-chart") echo 'selected="selected"' ?> value="fa fa-line-chart">&#xf201</option>
                        <option <?php if ($status->icon == "fa fa-location-arrow") echo 'selected="selected"' ?> value="fa fa-location-arrow">&#xf124</option>
                        <option <?php if ($status->icon == "fa fa-lock") echo 'selected="selected"' ?> value="fa fa-lock">&#xf023</option>
                        <option <?php if ($status->icon == "fa fa-mail-forward") echo 'selected="selected"' ?> value="fa fa-mail-forward">&#xf064</option>
                        <option <?php if ($status->icon == "fa fa-mail-reply") echo 'selected="selected"' ?> value="fa fa-mail-reply">&#xf112</option>
                        <option <?php if ($status->icon == "fa fa-mail-reply-all") echo 'selected="selected"' ?> value="fa fa-mail-reply-all">&#xf122</option>
                        <option <?php if ($status->icon == "fa fa-times") echo 'selected="selected"' ?> value="fa fa-times">&#xf00d</option>
                        <option <?php if ($status->icon == "fa fa-trash-o") echo 'selected="selected"' ?> value="fa fa-trash-o">&#xf014</option>
                        <option <?php if ($status->icon == "fa fa-user") echo 'selected="selected"' ?> value="fa fa-user">&#xf007</option>
                        <option <?php if ($status->icon == "fa fa-user-plus") echo 'selected="selected"' ?> value="fa fa-user-plus">&#xf234</option>
                        <option <?php if ($status->icon == "fa fa-user-secret") echo 'selected="selected"' ?> value="fa fa-user-secret">&#xf21b</option>
                        <option <?php if ($status->icon == "fa fa-user-times") echo 'selected="selected"' ?> value="fa fa-user-times">&#xf235</option>
                        <option <?php if ($status->icon == "fa fa-users") echo 'selected="selected"' ?> value="fa fa-users">&#xf0c0</option>
                        <option <?php if ($status->icon == "fa fa-wrench") echo 'selected="selected"' ?> value="fa fa-wrench">&#xf0ad</option>
                        <option <?php if ($status->icon == "fa fa-circle-o-notch") echo 'selected="selected"' ?> value="fa fa-circle-o-notch">&#xf1ce</option>
                        <option <?php if ($status->icon == "fa fa-refresh") echo 'selected="selected"' ?> value="fa fa-refresh">&#xf021</option>
                        <option <?php if ($status->icon == "fa fa-spinner") echo 'selected="selected"' ?> value="fa fa-spinner">&#xf110</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('icon_color') ? 'has-error' : '' }}">
                    <label>{!! Lang::get('lang.icon_color') !!}: <span class="text-red"> *</span></label><br>
                    <input type="text" name="icon_color" value="{!! $status->icon_color !!}" class="form-control  my-colorpicker1" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 form-group">
                {!! Form::label('visibility',Lang::get('lang.visibility_to_client')) !!}   <span class="text-red"> *</span>
                <div class="row">
                    <div class="col-xs-6">
                        {!! Form::radio('visibility_for_client',1, true ,['id' => 'state1', 'onclick' => 'handleClick(this);']) !!} {{Lang::get('lang.yes')}}
                    </div>
                    <div class="col-xs-6">
                        {!! Form::radio('visibility_for_client',0,false ,['id' => 'state1', 'onclick' => 'handleClick(this);']) !!} {{Lang::get('lang.no')}}
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="callout callout-default" style="font-style: oblique;">{!! Lang::get('lang.if_yes_status_name_will_be_displayed') !!}</div>
            </div>
            <div class="col-md-2 form-group">
                {!! Form::label('allow_client',Lang::get('lang.allow_client')) !!}   <span class="text-red"> *</span>
                <div class="row">
                    <div class="col-xs-6">
                        {!! Form::radio('allow_client',1,true,['id' => 'allow_client']) !!} {{Lang::get('lang.yes')}}
                    </div>
                    <div class="col-xs-6">
                        {!! Form::radio('allow_client',0,false,['id' => 'allow_client']) !!} {{Lang::get('lang.no')}}
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="callout callout-default" style="font-style: oblique;">{!! Lang::get('lang.if_yes_then_clients_can_choose_this_status') !!}</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 form-group">
                {!! Form::label('visibility',Lang::get('lang.purpose_of_status')) !!}  <span class="text-red"> *</span>
                <?php
                $status_types = App\Model\helpdesk\Ticket\TicketStatusType::where('id', '!=', 3)->get();
                ?>
                <select name="purpose_of_status" class="form-control"  id="purpose_of_status" onchange="changeStatusType()" required>
                    @foreach($status_types as $status_type)
                        <option value="{!! $status_type->id !!}"  <?php if($status->purpose_of_status == $status_type->id) { echo 'selected'; } ?> >{!! ucfirst($status_type->name) !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-8">
                <div class="callout callout-default" style="font-style: oblique;">{!! Lang::get('lang.purpose_of_status_will_perform_the_action_to_be_applied_on_the_status_selection') !!}</div>
            </div>
        </div>
        <div class="row" id="secondary" style="display:none;">
            <div class="col-md-4 form-group">
                {!! Form::label('visibility',Lang::get('lang.status_to_display')) !!}
                <select name="secondary_status" class="form-control">
                    @foreach($statusWithVisibility as $ticketStatus)
                        <option value="{!! $ticketStatus->id !!}"  <?php if($status->secondary_status == $ticketStatus->id) { echo 'selected'; } ?>>{!! ucfirst($ticketStatus->name) !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-8">
                <div class="callout callout-default" style="font-style: oblique;">{!! Lang::get('lang.this_status_will_be_displayed_to_client_if_visibility_of_client_chosen_no') !!}</div>
            </div>
        </div>
        <div class="row"  id="sending_email">
            <div class="col-md-6 form-group">
                {!! Form::label('send_email',Lang::get('lang.send_email')) !!}
                <div class="row">
                    <div class="col-xs-3">
                        {!! Form::checkbox("send[client]",'1',checkArray('client',$status->send_email)) !!} {{Lang::get('lang.client')}}
                    </div>
                    <div class="col-xs-3">
                        {!! Form::checkbox("send[assigned_agent_team]",'1',checkArray('assigned_agent_team',$status->send_email)) !!} {{Lang::get('lang.assigner')}}
                    </div>
                    <div class="col-xs-3">
                        {!! Form::checkbox("send[admin]",'1',checkArray('admin',$status->send_email)) !!} {{Lang::get('lang.admin')}}
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="callout callout-default" style="font-style: oblique;">{!! Lang::get('lang.tick_who_all_to_send_notification') !!}</div>
            </div>
        </div>
        <?php \Event::dispatch('status_sms_option', [[$status]]); ?>
        <div class="row">
            <div class="col-md-8 form-group">
                {!! Form::label('message',Lang::get('lang.message')) !!}
                <textarea name="message" class="form-control" id="ckeditor" style="height:100px;" >{!! $status->message !!}</textarea>
            </div>
            <div class="col-xs-4">
                <div class="callout callout-default" style="font-style: oblique;">{!! Lang::get('lang.this_message_will_be_displayed_in_the_thread_as_internal_note') !!}</div>
            </div>
            <div class="col-md-2 form-group">
                {!! Form::label('halt_sla',Lang::get('lang.halt_sla')) !!}  <span class="text-red"> *</span>
                <div class="row">
                    <div class="col-xs-6">
                        {!! Form::radio('halt_sla',1,true,['id' => 'halt_sla']) !!} {{Lang::get('lang.yes')}}
                    </div>
                    <div class="col-xs-6">
                        {!! Form::radio('halt_sla',0,false,['id' => 'halt_sla']) !!} {{Lang::get('lang.no')}}
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="callout callout-default" style="font-style: oblique;">{!! Lang::get('lang.if_required_comment_is_mandatory_while_changing_the_ticket_status') !!}</div>
            </div>
            <div class="col-md-2 form-group">
                {!! Form::label('comment',Lang::get('lang.comment')) !!}  <span class="text-red"> *</span>
                <div class="row">
                    <div class="col-xs-6">
                        {!! Form::radio('comment',1,true,['id' => 'comment']) !!} {{Lang::get('lang.required')}}
                    </div>
                    <div class="col-xs-6">
                        {!! Form::radio('comment',0,false,['id' => 'comment']) !!} {{Lang::get('lang.optional')}}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                {!! Form::label('allowed_override', Lang::get('lang.allowed_override_status_to')) !!}<br>
                {!! Form::radio('allowed_override', 'all', (count($target_status) == 0) ? true: false, ['class' => 'status-override', 'onclick' => 'handleOverride(this)']) !!}&nbsp;&nbsp;{!! Lang::get('lang.all_statuses') !!}&nbsp;&nbsp;&nbsp;&nbsp;
                {!! Form::radio('allowed_override', 'specific', (count($target_status) == 0) ? false: true, ['class' => 'status-override', 'onclick' => 'handleOverride(this)']) !!}&nbsp;&nbsp;{!! Lang::get('lang.specific_statuses') !!}
            </div>
        </div>
        <div class="row ">
            <div class="col-md-6 select-status">
                {!! Form::label('target_status[]', Lang::get('lang.select_status')) !!}<span class="text-red"> *</span>
                {!! Form::select('target_status[]', $all_status, $target_status, ['id' => 'target_status', 'class' => 'form-control', 'multiple' => true,  'required' => true]) !!}
            </div>
        </div>
        <div class="form-group">
            <input type="checkbox" name="default" <?php if($status->default == 1) { echo "checked='checked' value='1'"; } ?>> {{ Lang::get('lang.make_system_default_for_selected_purpose') }}
        </div>
    </div>
    <div class="box-footer">
            <button type="submit" id="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-refresh fa-spin fa-1x fa-fw'>&nbsp;</i> updating..."><i class="fa fa-refresh">&nbsp;&nbsp;</i>{!!Lang::get('lang.update')!!}</button>

    </div>
    {!! Form::close() !!}
</div>
<!-- bootstrap color picker -->
<script src="{{assetLink('js','bootstrap-colorpicker')}}"></script>
<script src="{{assetLink('js','select2')}}"></script>


<script>
function format(option){
    var icon = $(option.element).attr('value');
    return '<i class="'+icon+'" ></i> ';
}
$('.icons').select2({
        templateResult: format,
        templateSelection: format,
        escapeMarkup: function (m) {
                                    return m;
                                    }
})
var currentValue = 0;
function handleClick(myRadio) {
    currentValue = myRadio.value;
    if (currentValue == '1') {
        $("#secondary").hide();
    } else if (currentValue == '0') {
        $("#secondary").show();
    }
}
$(function(){
    var myRadio = {!! $status->visibility_for_client !!};
    currentValue = myRadio;
    if (currentValue == '1') {
        $("#secondary").hide();
    } else if (currentValue == '0') {
        $("#secondary").show();
    }
});
function handleOverride(myRadio){
    if (myRadio.value == 'all') {
        handleSelectStatusField('disabled', 'none');
    } else {
        handleSelectStatusField(false, 'block');
    }
}
function handleSelectStatusField($prop, $display) {
    $('#target_status').prop('disabled', $prop);
    $('.select-status').css('display', $display);
}
@if(count($target_status) > 0)
handleSelectStatusField(false, 'block');
@else
handleSelectStatusField('disabled', 'none');
@endif
// $(function(){
//     var purpose_of_status = document.getElementById('purpose_of_status').value;
//     if(purpose_of_status == 2) {
//         $('#sending_email').show();
//     } else {
//         $('#sending_email').hide();
//     }
// });
// function changeStatusType() {
//     var purpose_of_status = document.getElementById('purpose_of_status').value;
//     if(purpose_of_status == 2) {
//         $('#sending_email').show();
//     } else {
//         $('#sending_email').hide();
//     }
// }
//Colorpicker
$(".my-colorpicker1").colorpicker({format:'hex'});
$(document).ready(function(){
   $('.select2-selection__rendered').removeAttr("title");
   $(".icons").change(function(){
        $('.select2-selection__rendered').removeAttr("title");
    });
});
</script>
@stop