@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id','1')->value('name');
        $titleName = ($title) ? $title :"SUPPORT CENTER";
 ?>
@section('meta-tags')
<meta name="title" content="{!! Lang::get('lang.emails_list-page-title') !!} :: {!! strip_tags($titleName) !!} ">
<meta name="description" content="{!! Lang::get('lang.emails_list-page-description') !!}">
@stop


@section('Emails')
active
@stop

@section('emails-bar')
active
@stop

@section('emails')
class="active"
@stop

@section('HeadInclude')
@stop
<!-- header -->
@section('PageHeader')
<h1>{{Lang::get('lang.emails')}}</h1>
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
	<h2 class="box-title">{!! Lang::get('lang.list_of_emails') !!}</h2><a href="{{route('emails.create')}}" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus"></span> &nbsp;{{Lang::get('lang.create_email')}}</a></div>

<div class="box-body table-responsive">



    <?php
    $default_system_email = App\Model\helpdesk\Settings\Email::where('id', '=', '1')->first();
    if($default_system_email->sys_email) {
    	$default_email = $default_system_email->sys_email;
    } else {
    	$default_email = null;
    }
    ?>
    		<!-- table -->
				<table class="table table-bordered dataTable" style="overflow:hidden;">
	<tr>
							<th width="100px">{{Lang::get('lang.email')}}</th>
							<th width="100px">{{Lang::get('lang.priority')}}</th>
							<th width="100px">{{Lang::get('lang.department')}}</th>
							<th width="100px">{{Lang::get('lang.created')}}</th>
							<th width="100px">{{Lang::get('lang.last_updated')}}</th>
							<th width="100px">{{Lang::get('lang.action')}}</th>
						</tr>
						@foreach($emails as $email)
						<tr>

							<td><a href="{{route('emails.edit', $email->id)}}"> {{$email -> email_address }}</a>
							@if($default_email == $email->id) 
								( Default )
								<?php $disabled = 'disabled'; ?>
							@else
								<?php $disabled = ''; ?>
							@endif
							</td>
							<?php $priority = App\Model\helpdesk\Ticket\Ticket_Priority::where('priority_id','=',$email->priority)->first(); ?>
							@if($email->priority == null)
								<?php $priority = "<a href=". url('getticket') .">System Default</a>"; ?>
							@else 
								<?php $priority = ucfirst($priority->priority); ?>
							@endif
							<td>{!! $priority !!}</td>
							@if($email->department !== null)
								<?php  $department = App\Model\helpdesk\Agent\Department::where('id','=',$email->department)->first(); 
								$dept = $department->name; ?>
							@elseif($email->department == null)
								<?php  $dept = "<a href=". url('getsystem') .">System Default</a>"; ?>
							@endif

							<td>{!! $dept !!}</td>
							<td>{!! faveoDate($email->created_at) !!}</td>
							<td>{!! faveoDate($email->updated_at) !!}</td>
							<td>
							{!! Form::open(['route'=>['emails.destroy', $email->id],'method'=>'DELETE']) !!}
                                                        
                                                         <?php
                                                             $url = url('delete/emailpopup/'.$email->id);
                                                             $confirmation = deletePopUp($email->id, $url, "Delete",'btn btn-primary btn-xs  ' . $disabled);
                                                          ?>
                                                        
                                                        <a href="{{route('emails.edit', $email->id)}}" class="btn btn-primary btn-xs "><i class="fa fa-edit" style="color:white;"> &nbsp;</i> Edit</a>&nbsp;&nbsp;{!!$confirmation!!}
		
							{!! Form::close() !!}
							</td>
						</tr>
						@endforeach
</table>
</div>
</div>
</div>
</div>
@stop
