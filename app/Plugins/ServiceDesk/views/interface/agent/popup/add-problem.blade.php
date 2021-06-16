<?php
    $sdPolicy = new App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy();
?>
<link href="{{assetLink('css','bootstrap-datetimepicker4')}}" rel="stylesheet" type="text/css" />
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>



<style>
    .table .table-bordered {
        width: 100%     !important;
    },
    .left-hand{
        float: left;
    }
</style>
<?php $exists=0; ?>

<div class="btn-group">
    <button type="button" class="btn btn-sm btn-primary" data-toggle="dropdown">{{Lang::get('ServiceDesk::lang.problem')}} &nbsp;&nbsp; <span class="caret"></span></button>
   <!--  <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button> -->
    <ul class="dropdown-menu" role="menu">
         @if($sdPolicy->problemCreate())
         <li><a href="#problem" data-toggle="modal" data-target="#problemnew{{$id->ticket_id}}">{{Lang::get('ServiceDesk::lang.attach_new_problem')}}</a></li>
         @endif
         @if($sdPolicy->problemAttach())
         <li><a href="#problem"  data-toggle="modal" data-target="#problemexisting{{$id->ticket_id}}">{{Lang::get('ServiceDesk::lang.attach_existing_problem')}}</a></li>
         @endif
    </ul>
</div>
<div class="modal fade" id="problemnew{{$id->ticket_id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.new_problem')}}</h4>
            </div>
            <div class="modal-body">
                <?php
                $subject = \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::getSubjectByThreadId($id);
                $content = \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::getBodyByThreadMaxId($id);
                $requester = \App\User::pluck('email', 'email')->toArray();
                $status = \App\Model\helpdesk\Ticket\Ticket_Status::pluck('name', 'id')->toArray();
                $priority = \App\Model\helpdesk\Ticket\Ticket_Priority::pluck('priority', 'priority_id')->toArray();
                $impact = App\Plugins\ServiceDesk\Model\Problem\Impact::pluck('name', 'id')->toArray();
                //$group = \App\Model\helpdesk\Agent\Permission::pluck('name', 'id')->toArray();
                $agent = \App\User::where('role', '!=', 'user')->pluck('email', 'id')->toArray();
                $assets = \App\Plugins\ServiceDesk\Model\Assets\SdAssets::pluck('name', 'id')->toArray();
                // $location = App\Plugins\ServiceDesk\Model\Assets\SdLocations::pluck('title', 'id')->toArray();
                $location = App\Location\Models\Location::pluck('title', 'id')->toArray();
                $ticket = App\Plugins\ServiceDesk\Controllers\Library\UtilityController::getTicketByThreadId($id);
                $departments = App\Plugins\ServiceDesk\Model\Assets\Department::pluck('name', 'id')->toArray();
                ?>
                <!-- Form  -->
                {!! Form::open(['url'=>'service-desk/attach-problem/ticket/new','files'=>true]) !!}
                {!! Form::hidden('ticketid',$id->ticket_id) !!}
                <div class="row">


                    <div class="form-group col-md-6 {{ $errors->has('from') ? 'has-error' : '' }}">

                        {!! Form::label('from',Lang::get('ServiceDesk::lang.from')) !!} <span class="text-red"> *</span></label>
                       <!--  {!! Form::select('from',$requester,null,['class' => 'form-control']) !!} -->
                        
                          {!!Form::select('from',[Lang::get('lang.from')=>''],null,['class' => 'form-control','id'=>'assign-from','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true','required']) !!}

                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('subject') ? 'has-error' : '' }}">

                        {!! Form::label('subject',Lang::get('ServiceDesk::lang.subject')) !!} <span class="text-red"> *</span></label>
                        {!! Form::text('subject',$subject,['class' => 'form-control','required']) !!}

                    </div>
                    <div class="form-group col-md-12 {{ $errors->has('description') ? 'has-error' : '' }}">
                        {!! Form::label('description',Lang::get('ServiceDesk::lang.description')) !!}
                        {!! Form::text('description',$content,['class' => 'form-control','id'=>'ckeditor1','required']) !!}
                    </div>
                    <div class="form-group col-md-4 {{ $errors->has('status_type_id') ? 'has-error' : '' }}">
                     {!! Form::label('status_type_id',Lang::get('ServiceDesk::lang.status')) !!}<span class="text-red"> *</span>
                        {!! Form::select('status_type_id',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.status')=>$status],null,['class'=>'form-control','required']) !!}
                    </div>
                    <div class="form-group col-md-4 {{ $errors->has('priority_id') ? 'has-error' : '' }}">
                        {!! Form::label('priority_id',Lang::get('ServiceDesk::lang.priority')) !!}<span class="text-red"> *</span>
                        {!! Form::select('priority_id',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.priorities')=>$priority],null,['class'=>'form-control','required']) !!}
                    </div>
                    <div class="form-group col-md-4 {{ $errors->has('impact_id') ? 'has-error' : '' }}">
                        {!! Form::label('impact_id',Lang::get('ServiceDesk::lang.impact')) !!}<span class="text-red"> *</span>
                        {!! Form::select('impact_id',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.impact')=>$impact],null,['class'=>'form-control','required']) !!}
                    </div>
                     <div class="form-group col-md-6 {{ $errors->has('department') ? 'has-error' : '' }}">
                        <label class="control-label">{{Lang::get('ServiceDesk::lang.department')}}</label><span class="text-red"> *</span>
                       {!! Form::select('department',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.departments')=>$departments],null,['class'=>'form-control','required']) !!}

                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('location_type_id') ? 'has-error' : '' }}">
                        {!! Form::label('location_type_id',Lang::get('ServiceDesk::lang.location')) !!}
                        {!! Form::select('location_type_id',[''=>Lang::get('lang.select')]+$location,null,['class'=>'form-control']) !!}
                    </div>
                   
                    {{--<div class="col-md-6 {{ $errors->has('group_id') ? 'has-error' : '' }}">
                        {!! Form::label('group_id',Lang::get('ServiceDesk::lang.group')) !!}
                        {!! Form::select('group_id',$group,null,['class' => 'form-control']) !!}
                    </div>--}}
                   
                    <div class="form-group col-md-6 {{ $errors->has('assigned_id') ? 'has-error' : '' }}">
                        {!! Form::label('assigned_id',Lang::get('ServiceDesk::lang.assigned')) !!}
                     <!--    {!! Form::select('assigned_id',$agent,null,['class' => 'form-control']) !!} -->

                      
                      {!!Form::select('assigned_id',[Lang::get('lang.assigned')=>''],null,['class' => 'form-control select2-agent','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}

                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('asset') ? 'has-error' : '' }}">
                        {!! Form::label('asset',Lang::get('ServiceDesk::lang.asset')) !!}
                
                       {!!Form::select('asset[]',[Lang::get('lang.asset')=>''],null,['class' => 'form-control select2','id'=>'assetlist','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}
                    </div>

                    <div class="form-group col-md-6 {{ $errors->has('attachment') ? 'has-error' : '' }}">
                        {!! Form::label('attachment',Lang::get('ServiceDesk::lang.attachment')) !!}
                        {!! Form::file('attachment[]',null,['class' => 'form-control']) !!}
                    </div>



                </div>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default left-hand" data-dismiss="modal"><i class="fa fa-times">&nbsp;</i>{!! Lang::get('ServiceDesk::lang.close') !!}</button>
             <!--    <input type="submit" class="btn btn-primary" value="{{Lang::get('lang.save')}}">
                --> 
                 {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
            </div>

            {!! Form::close() !!}
            <!-- /Form -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->  


<!-- Existing Problem-->
<div class="modal fade" id="problemexisting{{$id->ticket_id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{url('service-desk/attach-problem/ticket/existing')}}">
                <input type="hidden" name="ticketid" value="{{$id->ticket_id}}">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{!! Lang::get('ServiceDesk::lang.existing_problems') !!}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                         <table id="addproblem-table1" class="table display" cellspacing="0" width="100%" styleClass="borderless">

                    <thead><tr>
                            <th></th>
                            <th>{!! Lang::get('ServiceDesk::lang.subject') !!}</th>
                            <th>{!! Lang::get('ServiceDesk::lang.status') !!}</th>
                         </tr>
                  </thead>


                </table>
                       
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default left-hand" data-dismiss="modal"><i class="fa fa-times">&nbsp;</i>{{Lang::get('ServiceDesk::lang.close')}}</button>
               <!--  <input type="submit" class="btn btn-primary" value="{{Lang::get('lang.save')}}"> -->
                 {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
            </div>

            </form>
            <!-- /Form -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->  

 
@push('scripts')

  <script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
  <script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
  <script src="{{assetLink('js','select2')}}"></script>
<script>
$(function(){
    $('.select2-agent').select2({
       
        maximumSelectionLength: 1,
        minimumInputLength: 1,
        ajax: {

            url: '{{url("ticket/form/requester?type=agent")}}',

            dataType: 'json',
            data: function(params) {
                // alert(params);
                return {
                    term: $.trim(params.term)
                };
            },
             processResults: function(data) {
                return{
                 results: $.map(data, function (value) {
                    return {
                        image:value.profile_pic,
                        text:value.first_name+" "+value.last_name,
                        id:value.id,
                        email:value.email,
                    }
                })
               }
            },
            cache: true
        },
         templateResult: formatState,
    });
    function formatState (state) { 
       
       var $state = $( '<div><div style="width: 20%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 78%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
        return $state;
  }
});
                $('#addproblem-table1').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('attach.problem.ticket.existing.ajax') !!}',
                    "oLanguage": {
                        "sLengthMenu": "_MENU_ Records per page",
                        "sSearch"    : "Search: ",
                        "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
                    },
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'subject', name: 'subject'},
                        {data: 'status', name: 'status'},
                    ],
                    "fnDrawCallback": function( oSettings ) {
                        $('.loader').css('display', 'none');
                    },
                    "fnPreDrawCallback": function(oSettings, json) {
                        $('.loader').css('display', 'block');
                    },
                });
          
            $(document).ready(function () { /// Wait till page is loaded
                    $('#click').click(function () {
                        $('#refresh').load('open #refresh');
                        $("#show").show();
                    });
                });
         </script>
<!--<script type="text/javascript">
    $(document).ready(function () {
        $('#form').submit(function () {
            var duedate = document.getElementById('datemask').value;
            if (duedate) {
                var pattern = /^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/;
                if (pattern.test(duedate) === true) {
                    $('#duedate').removeClass("has-error");
                    $('#clear-up').remove();
                } else {
                    $('#duedate').addClass("has-error");
                    $('#clear-up').remove();
                    $('#box-header1').append("<div id='clear-up'><br><br><div class='alert alert-danger alert-dismissable'><i class='fa fa-ban'></i><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button> Invalid Due date</div></div>");
                    return false;
                }
            }
        });
    });
</script>-->

<script>
    $(function(){
    $('#assign-from').select2({
       
        maximumSelectionLength: 1,
        minimumInputLength: 1,
        ajax: {

            url: '{{url("ticket/form/requester?type=requester")}}',

            dataType: 'json',
            data: function(params) {
                // alert(params);
                return {
                    term: $.trim(params.term)
                };
            },
             processResults: function(data) {
                return{
                 results: $.map(data, function (value) {
                    return {
                        image:value.profile_pic,
                        text:value.first_name+" "+value.last_name,
                        id:value.email,
                        email:value.email,
                    }
                })
               }
            },
            cache: true
        },
         templateResult: formatState,
    });
    function formatState (state) { 
       
       var $state = $( '<div><div style="width: 20%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 78%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
        return $state;
  }
});

$(function(){
    $('#assetlist').select2({
       
        maximumSelectionLength: 10,
        minimumInputLength: 1,
        ajax: {

            url: '{{url("service-desk/get/assetlist")}}',

            dataType: 'json',
            data: function(params) {
                // alert(params);
                return {
                    term: $.trim(params.term)
                };
            },
             processResults: function(data) {
                return{
                 results: $.map(data, function (value) {
                    return {
                        text:value.name,
                        id:value.id,
                    }
                })
               }
            },
            cache: true
        },
    });
});

</script>
<script type="text/javascript">
     if('{{Lang::getLocale()}}'=='ar'){
      setTimeout(function(){
         $('.left-hand').css('float','right');
    },100)
  } else {
     $('.left-hand').css('float','left');
  }
</script> 

 @endpush

