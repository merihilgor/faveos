<?php
    $assetTypes = App\Plugins\ServiceDesk\Model\Assets\SdAssettypes::pluck('name', 'id')->toArray();
    $organization = App\Plugins\ServiceDesk\Model\Common\SdOrganization::pluck('name', 'id')->toArray();
?>
<style>
    .table .table-bordered {
        width: 100%     !important;
    },
     .left-hand{
        float: left;
    }
</style>
<head>
  <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
  <style type="text/css">
      .timeline:before {
      background: #dce1dc !important;  
    }
  </style>
</head>

<div class="modal fade" id="attachAsset">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{!! Lang::get('ServiceDesk::lang.assets') !!}</h4>
            </div>
            <div class="modal-body">
                <!-- Form  -->
                <div class="row">
                    <div class="col-md-12 ui-widget">
                         <label class=" control-label">{{Lang::get('ServiceDesk::lang.asset_type')}}</label>&nbsp;&nbsp;&nbsp;<a href="#" data-toggle="tooltip" title="{!! Lang::get('ServiceDesk::lang.select_asset_based_on_asset-types') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a></label>
                        {!! Form::select('asset_type',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.choose')=>$assetTypes],null,['class'=>'form-control','id'=>'asset_type']) !!}
                    </div>
                    <div class="col-md-12 ui-widget">
                        <label class="control-label">{{Lang::get('ServiceDesk::lang.used_by')}}</label>&nbsp;&nbsp;&nbsp;<a href="#" data-toggle="tooltip" title="{!! Lang::get('ServiceDesk::lang.select_asset_based_on_used_by') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a></label>
                        {!!Form::select('used_by',[Lang::get('lang.usedby')=>''],null,['class' => 'form-control select2','id'=>'used_by','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}
                    </div>
                    <div class="col-md-12 ui-widget">
                        <label class="control-label">{{Lang::get('ServiceDesk::lang.managed_by')}}</label>&nbsp;&nbsp;&nbsp;<a href="#" data-toggle="tooltip" title="{!! Lang::get('ServiceDesk::lang.select_asset_based_on_managed_by') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a></label>
                        {!!Form::select('managed_by',[Lang::get('lang.managedby')=>''],null,['class' => 'form-control select2','id'=>'managed_by','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}
                    </div>
                    <div class="col-md-12 ui-widget">
                         <label class=" control-label">{{Lang::get('ServiceDesk::lang.organization')}}</label>&nbsp;&nbsp;&nbsp;<a href="#" data-toggle="tooltip" title="{!! Lang::get('ServiceDesk::lang.select_asset_based_on_organization') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a> 
                        {!! Form::select('organization',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.choose')=>$organization],null,['class'=>'form-control','id'=>'organization']) !!}
                    </div>
                    <div class="col-md-12 ui-widget">
                        <a href="#"  class="btn btn-primary" onclick="assetAttach()"><i class="fa fa-check"></i> {{Lang::get('ServiceDesk::lang.apply')}}</button></a>
                    </div>
                    <div class="col-md-12">
                        <br>
                        {!! Form::open(['url'=>'service-desk/asset/attach/contracts', 'name' => 'assest-attach', 'id' => 'assest-attach']) !!}
                        {!! Form::hidden('contractid',$contract->id) !!}

                        <table id="asset-attach-table1" class="table display" cellspacing="0" width="100%" styleClass="borderless">
                               <thead><tr>
                                        <th>#</th>
                                        <th>{!! Lang::get('ServiceDesk::lang.assets') !!}</th>
                                        <th>{!! Lang::get('ServiceDesk::lang.used_by') !!}</th>
                                    </tr>
                                </thead>
                        </table>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button"  class="btn btn-primary left-hand" data-dismiss="modal"><i class="fa fa-times">&nbsp;</i>{!! Lang::get('ServiceDesk::lang.close') !!}</button>
                <!-- <input type="submit" class="btn btn-primary" value="{{Lang::get('lang.save')}}"> -->
                 {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
            </div>

            {!! Form::close() !!}
            <!-- /Form -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->  
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}" type="text/javascript"></script>
<script>
    var assetTypeId='',usedById='',managedById='',organizationId='';
    
    document.getElementById('asset_type').onchange = function () {
        assetTypeId = $('#asset_type').val();
    }
    document.getElementById('used_by').onchange = function () {
        usedById = $('#used_by').val();
    }
    document.getElementById('managed_by').onchange = function () {
        managedById = $('#managed_by').val();
    }
    document.getElementById('organization').onchange = function () {
        organizationId = $('#organization').val();
    }

   function assetAttach() {
    $('#asset-attach-table1').DataTable({
        processing: true,
        serverSide: true,
        bDestroy: true,
        ajax: "{!! url('service-desk/attach-assets/contract/') !!}"+'?asset_type_id='+assetTypeId+'&used_by_id='+usedById+'&managed_by_id='+managedById+'&organization_id='+organizationId,
        "oLanguage": {
            "sLengthMenu": "_MENU_ Records per page",
            "sSearch"    : "Search: ",
            "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! asset("lb-faveo/media/images/gifloader3.gif") !!}">'
        },
        
        columns: [
        {data: 'id', name: 'id'},
        {data: 'name', name: 'name'},
        {data: 'used_by', name: 'used_by'},
        ],
        "fnDrawCallback": function( oSettings ) {
            $('.loader').css('display', 'none');
        },
        "fnPreDrawCallback": function(oSettings, json) {
            $('.loader').css('display', 'block');
        },
    });
 };
</script>
<script type="text/javascript">
    $(document).ready(function () { /// Wait till page is loaded
        $('#click').click(function () {
            $('#refresh').load('open #refresh');
            $("#show").show();
        });
    });
   if('{{Lang::getLocale()}}'=='ar'){
      setTimeout(function(){
       $('.left-hand').css('float','right');
   },100)
  } else {
   $('.left-hand').css('float','left');
}
</script>
<script src="{{assetLink('js','select2')}}"></script>
<script>
    $('#managed_by').select2({
       
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
       
       var $state = $( '<div><div style="width: 8%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 90%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
        return $state;
  }
</script>
<script>
    $('#used_by').select2({
       
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
       
       var $state = $( '<div><div style="width: 8%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 90%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
        return $state;
  }
</script>