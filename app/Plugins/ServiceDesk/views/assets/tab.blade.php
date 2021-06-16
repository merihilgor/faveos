<?php
$contract = $contract = (new \App\Plugins\ServiceDesk\Controllers\Assets\ApiAssetController())->getContractBasedOnAsset($asset);
?>

<head>
   <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
   <style type="text/css">
      .timeline:before {
   
     background: #dce1dc !important;  
}
  </style>
</head>

<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">{{ Lang::get('ServiceDesk::lang.associated_requestes') }}</a></li>
                <li><a href="#tab_2" data-toggle="tab">{{ Lang::get('ServiceDesk::lang.associated_contracts') }}</a></li>
            </ul>
            <div class="tab-content">

                <div class="tab-pane active" id="tab_1">
                    <div class="box box-primary">
                        <div class="box-body">
                          @if(count($asset->requests())>0)
                          <table id="request-table" class="table display" cellspacing="0" width="100%" styleClass="borderless">
                             <thead><tr>
                                <th>{!! Lang::get('ServiceDesk::lang.subject') !!}</th>
                                <th>{!! Lang::get('ServiceDesk::lang.request') !!}</th>
                                <th>{!! Lang::get('ServiceDesk::lang.status') !!}</th>
                                <th>{!! Lang::get('ServiceDesk::lang.created') !!}</th>
                            </tr>
                        </thead>
                    </table>
                    @else
                    {!! Lang::get('ServiceDesk::lang.no_data_available_in_table') !!}
                    @endif
                </div>
                <!-- /.box-body -->
            </div>
        </div>


        <div class="tab-pane" id="tab_2">

            <div class="box box-primary">
             <div class="box-body">
        @if($contract)
              <div class="tab-pane active" id="timeline">
                 <!-- The timeline -->
                 <ul class="timeline timeline-inverse">
                  <!-- timeline time label -->
                  <li class="time-label">
                   <span class="bg-green"><i class="fa fa-clock-o"></i>&nbsp;
                       {{ $contract['created_at'] }}
                   </span>
               </li>
               <li>
                  <i class="fa fa-paperclip bg-green"></i>
                  <div class="timeline-item">
                     <h3 class="timeline-header no-border"><a style="pointer-events: none">Contract Name</a> </h3>
                     <table class="table table-bordered">
                        <tbody>
                            <tr>
                             <td> <b style="font-size: 15px">#CNTR-{{ $contract['id'] }}</b> &nbsp;&nbsp; {{ $contract['name'] }} </td>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </li>

         <li>
          <i class="fa fa-info-circle bg-green"></i>
          <div class="timeline-item">
             <h3 class="timeline-header no-border"><a style="pointer-events: none">Contract Details</a> </h3>
             <table class="table table-bordered">
                <tbody>
                    <tr>
                      <th>{{ Lang::get('ServiceDesk::lang.cost') }}</th>
                      <td>{{ $contract['cost'] }}</td>
                      <th>{{ Lang::get('ServiceDesk::lang.approver') }}</th>
                      <td>{{ $contract['approver']['name'] }}</td>
                  </tr>
                  <tr>
                     <th>{{ Lang::get('ServiceDesk::lang.vendor') }}</th>
                     <td>{{ $contract['vendor']['name'] }}</td>
                     <th>{{ Lang::get('ServiceDesk::lang.contract_start_date') }}</th>
                     <td>{{ $contract['contract_start_date'] }}</td>
                 </tr>
                 <tr>
                    <th>{{ Lang::get('ServiceDesk::lang.contract_end_date') }}</th>
                    <td>{{ $contract['contract_end_date'] }}</td>
                    <th>{{ Lang::get('ServiceDesk::lang.notify_before') }} <span data-toggle="tooltip" style="color: #3c8dbc;" title="{!! Lang::get('ServiceDesk::lang.notify_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></span></th>
                    <td>{{ $contract['notify_before'] }}</td>
                </tr>  
            </tbody>
        </table>
    </div>
</li>
</div>
<!-- /.box-body -->
</div>
@else
    {!! Lang::get('ServiceDesk::lang.no_data_available_in_table') !!}
@endif
<!-- /.box -->

</div>
</div>
</div>
<!-- /.tab-content -->
</div>
<!-- nav-tabs-custom -->
</div>
<!-- /.col -->
</div>
<!-- /.row -->

<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>

<script>
    $('#request-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! url("service-desk/assets/requesters?assetid=". $asset->id) !!}',
        "lengthMenu": [[5, 10], [5, 10]],
        "oLanguage": {
            "sLengthMenu": "_MENU_ Records per page",
            "sSearch"    : "Search: ",
            "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink("image","gifloader3") !!}">'
        },
        columns: [
        {data: 'subject', name: 'subject'},
        {data: 'request', name: 'request'},
        {data: 'status', name: 'status'},
        {data: 'created', name: 'created'},

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



