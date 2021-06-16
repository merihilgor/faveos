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


<meta name="title" content="{!! Lang::get('lang.status_lists-page-title') !!} :: {!! strip_tags($title_name) !!} ">

<meta name="description" content="{!! Lang::get('lang.status_lists-page-description') !!}">


@stop

@section('custom-css')
    <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
@stop

@section('Tickets')
active
@stop

@section('status')
class="active"
@stop

@section('PageHeader')
<h1>{!! Lang::get('lang.status') !!}</h1>
@stop

@section('content')
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
        <h3 class="box-title">{!! Lang::get('lang.list_of_status') !!}</h3>
        <div class="box-tools pull-right">
            <a href="{!! URL::route('statuss.create') !!}"><button class="btn btn-primary btn-sm" id="create" title="{!! Lang::get('lang.create') !!}">
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;
                    {!! Lang::get('lang.create_status') !!}</button></a>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body">
        <?php
        $status_type = new App\Model\helpdesk\Ticket\TicketStatusType();
        ?>
        <table class="table table-bordered" id="chumper">
            <thead>
                <tr>
                    <td>{!! Lang::get('lang.name') !!}</td>
                    <td>{!! Lang::get('lang.visible_to_client') !!}</td>
                    <td>{!! Lang::get('lang.purpose_of_status') !!}</td>
                    <td>{!! Lang::get('lang.send_email') !!}</td>
                    <td>{!! Lang::get('lang.order') !!}</td>
                    <td>{!! Lang::get('lang.icon') !!}</td>
                    <td>{!! Lang::get('lang.action') !!}</td>
                </tr>
            </thead>
        </table>
    </div><!-- /.box-body -->
</div>

<div class="modal fade" id="delete">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{!! Lang::get('lang.delete') !!}</h4>
                 <div id="replaceMessage"></div>
                 <div id="error"></div>
            </div>
           
            <div class="modal-body">
                <p>{!! trans('lang.are_you_sure_you_want_to_delete') !!} </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>Close</button>
                <button class="btn btn-danger" id="delete-url"><i class="fa fa-trash" aria-hidden="true">&nbsp;&nbsp;</i>{!! Lang::get('lang.delete') !!}</button>
            </div>
        </div> 
    </div>
</div>
@stop
@push('scripts')
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
@include('vendor.yajra.status-javascript')
<script type="text/javascript">
    function showConfirmModer($id){
       $("#delete").modal("show");
        $("#delete-url").click(function(){
          $("#delete-url").attr('disabled',true);
           $("#delete-url").html("<i class='fa fa-refresh fa-spin fa-1x fa-fw'></i>Please Wait...");
                  $.ajax({
                       type: 'delete',
                       url: '{!! url("status-delete") !!}'+'/'+$id,
                       data: {
                          "_token": "{{ csrf_token() }}",
                       },
                       success: function(data) {  
                        if (data.success == true ){
                               var result =  '<div class="alert alert-success alert-dismissable"><strong><i class="fa fa-check"></i> Success! </strong> '+data.message+' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
                               $("#delete-url").html("<i class='fa fa-trash'>&nbsp;&nbsp;</i>Delete");
                                $('#replaceMessage').html(result);
                                $('#replaceMessage').css('color', 'green');
                                $("#delete-url").attr('disabled',false);
                              setTimeout(function(){
                                  window.location.reload();
                              },2000);
                            } else {
                            var html = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>Whoops! </strong>Something went wrong<br><br><ul><li>'+data.message+'</li>';
                             html += '</ul></div>';
                             $('#replaceMessage').hide();
                             $('#error').show();
                              document.getElementById('error').innerHTML = html;
                              $("#delete-url").html("<i class='fa fa-trash'>&nbsp;&nbsp;</i>Delete");
                               $("#delete-url").attr('disabled',false);
                               setTimeout(function(){
                                  window.location.reload();
                              },2000);
                                }
                               },
                               error: function(data) {
                                console.log('dsfsdfs')
                               var html = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>Whoops! </strong>Something went wrong<br><br><ul><li>';
                                for (key in data.responseJSON.message) {
                                  html +=  data.responseJSON.message[key][0] 
                                }
                                html += '</li></ul></div>';
                                $('#replaceMessage').hide();
                                $('#error').show();
                                document.getElementById('error').innerHTML = html;
                                $("#delete-url").html("<i class='fa fa-trash'>&nbsp;&nbsp;</i>Delete");
                                 $("#delete-url").attr('disabled',false);
                                 setTimeout(function(){
                                    window.location.reload();
                                },2000);
                                }
                              });
                          });
                      }



</script>
@endpush