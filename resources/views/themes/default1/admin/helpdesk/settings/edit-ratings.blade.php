@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')


<meta name="title" content="{!! Lang::get('lang.ratings_edit-page-title') !!} :: {!! strip_tags($titleName) !!} ">

<meta name="description" content="{!! Lang::get('lang.ratings_edit-page-description') !!}">


@stop

@section('Tickets')
active
@stop

@section('ratings')
class="active"
@stop

@section('HeadInclude')
@stop

<!-- header -->
@section('PageHeader')
<h1>{!! Lang::get('lang.ratings') !!}</h1>
@stop

<!-- content -->
@section('content')
{!! Form::model($rating,['route'=>['settings.rating', $rating->id],'method'=>'PATCH','files' => true,'id'=>'Form']) !!}
@if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <i class="fa fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        @if(Session::has('errors'))
        <?php //dd($errors); ?>
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <br/>
            @if($errors->first('name'))
            <li class="error-message-padding">{!! $errors->first('name', ':message') !!}</li>
            @endif
            @if($errors->first('display_order'))
            <li class="error-message-padding">{!! $errors->first('display_order', ':message') !!}</li>
            @endif
            @if($errors->first('rating_scale'))
            <li class="error-message-padding">{!! $errors->first('rating_scale', ':message') !!}</li>
            @endif
            @if($errors->first('rating_area'))
            <li class="error-message-padding">{!! $errors->first('rating_area', ':message') !!}</li>
            @endif
            @if($errors->first('restrict'))
            <li class="error-message-padding">{!! $errors->first('restrict', ':message') !!}</li>
            @endif
            @if($errors->first('allow_modification'))
            <li class="error-message-padding">{!! $errors->first('allow_modification', ':message') !!}</li>
            @endif
        </div>
        @endif
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{{Lang::get('lang.edit_ratings')}}</h3>
    </div>
    <div class="box-body">
        
        <div class="row">
            <div class="col-md-6 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                {!! Form::label('name',Lang::get('lang.rating_label')) !!}<span style="color:red;">*</span>
                {!! Form::text('name',null,['class' => 'form-control']) !!}
            </div>
            <div class="col-md-6 form-group {{ $errors->has('display_order') ? 'has-error' : '' }}">
                {!! Form::label('display_order',Lang::get('lang.display_order')) !!}<span style="color:red;">*</span>
                {!! Form::input('number','display_order',null,['class' => 'form-control','id' =>'test','min' =>'1']) !!}
            </div>
        </div>
        <div class="form-group {{ $errors->has('rating_scale') ? 'has-error' : '' }}">
            {!! Form::label('rating_scale',Lang::get('lang.rating_scale')) !!}<span style="color:red;">*</span>
            <div class="callout callout-default" style="font-style: oblique;">{!! Lang::get('lang.rating-msg1') !!}</div>
            {!! Form::select('rating_scale',['1' => '1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8'],null,['class' => 'form-control']) !!}
        </div>
        <div class="form-group {{ $errors->has('rating_area') ? 'has-error' : '' }}">
            {!! Form::label('rating_area',Lang::get('lang.rating_area')) !!}<span style="color:red;">*</span>
            {!! Form::select('rating_area',['Helpdesk Area' => 'Helpdesk Area','Comment Area'=>'Comment Area'],$rating->rating_area,['class' => 'form-control' , 'disabled' => true]) !!}
        </div>
        <div class="form-group {{ $errors->has('restrict') ? 'has-error' : '' }}">
            <!-- gender -->
            {!! Form::label('gender',Lang::get('lang.rating_restrict')) !!}<span style="color:red;">*</span>
            <div class="callout callout-default" style="font-style: oblique;">{!! Lang::get('lang.rating-msg2') !!}</div>

              {!! Form::select('restrict',['General' => 'General',Lang::get('lang.department')=>$department],$restrict[0],['class' => 'form-control']) !!}
           
             

           
        </div>
        <div class="form-group {{ $errors->has('allow_modification') ? 'has-error' : '' }}">
            <!-- Email user -->
            {!! Form::label('allow_modification',Lang::get('lang.rating_change')) !!}<span style="color:red;">*</span>
            <div class="callout callout-default" style="font-style: oblique;">{!! Lang::get('lang.rating-msg3') !!}</div>
            <div class="row">
                <div class="col-xs-2">
                    {!! Form::radio('allow_modification', 1,  $rating->allow_modification) !!} {{Lang::get('lang.yes')}}
                </div>
                <div class="col-xs-2">
                    {!! Form::radio('allow_modification', 0, !$rating->allow_modification) !!} {{Lang::get('lang.no')}}
                </div>
            </div>        
        </div>
        @if ($rating->id == 1)
         <div class="form-group {{ $errors->has('rating_icon') ? 'has-error' : '' }}">
            <!-- Email user -->
            {!! Form::label('rating_icon',Lang::get('lang.rating_icon_in_mails')) !!}<span style="color:red;">*</span>
            <div class="callout callout-default" style="font-style: oblique;">{!! Lang::get('lang.rating-icon-mail-msg') !!}</div>
               {!! Form::select('rating_icon',['star' => 'star (???)', 'star-o' => 'star-o (???)'], $rating->rating_icon,['class' => 'form-control']) !!}
        </div>
        @endif
    </div>
    <div class="box-footer">
 <button type="submit" id="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-refresh fa-spin fa-1x fa-fw'>&nbsp;</i> updating..."><i class="fa fa-refresh">&nbsp;&nbsp;</i>{!!Lang::get('lang.update')!!}</button>    
    </div>
</div>
{!! Form::close() !!}

<script>
 
$("#test").keyup(function() {
    var val = $("#test").val();
    if(parseInt(val) < 0 || isNaN(val)) {
    alert("please enter valid values");
        $("#test").val("");
        $("#test").focus();
    }
});
</script>
@stop