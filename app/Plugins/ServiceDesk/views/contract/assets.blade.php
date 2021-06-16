<?php
    $contractThread = new \App\Plugins\ServiceDesk\Controllers\Contract\ContractThreadController();
    $response = $contractThread->getcontractThreads($contract->id);
    $data = json_decode($response->content())->data->contract_threads;
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
                <li class="active"><a href="#tab_1" data-toggle="tab">{{ Lang::get('ServiceDesk::lang.associated_assets') }}</a></li>
                <li><a href="#tab_2" data-toggle="tab">{{ Lang::get('ServiceDesk::lang.notify_agents') }}</a></li>
                <li><a href="#tab_3" data-toggle="tab">{{ Lang::get('ServiceDesk::lang.contract_history') }}</a></li>
            </ul>
             <!-- /.tab-pane 1-->
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="box box-primary">
                        <div class="box-body">
                          
                          <table id="problem-table" class="table display" cellspacing="0" width="100%" styleClass="borderless">
                               <thead><tr>
                                        <th>{!! Lang::get('ServiceDesk::lang.name') !!}</th>
                                        <th>{!! Lang::get('ServiceDesk::lang.managed_by') !!}</th>
                                        <th>{!! Lang::get('ServiceDesk::lang.used_by') !!}</th>
                                        <th>{!! Lang::get('ServiceDesk::lang.action') !!}</th>
                                    </tr>
                                </thead>
                         </table>                    
                      </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.tab-pane 2 -->
                <div class="tab-pane" id="tab_2">
                  <div class="tab-pane active" id="tab_1">
                    <div class="box box-primary">
                        <div class="box-body">
                          
                          <table id="notify-table" class="table display" cellspacing="0" width="100%" styleClass="borderless">
                               <thead><tr>
                                        <th>{!! Lang::get('ServiceDesk::lang.name') !!}</th>
                                    </tr>
                                </thead>
                         </table>
                      </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                 </div>
                </div>

                  <!-- /.tab-pane 3 -->
                <div class="tab-pane" id="tab_3">
                  <div class="tab-pane active" id="tab_1">
                     
                        <div class="box box-primary">
                            <div class="box-body">
                            @if($data)
                                @foreach($data as $contractThreads)

                                    <!-- The timeline -->
                                    <ul class="timeline timeline-inverse">
                                    <li class="time-label">
                                         <span class="bg-green"><i class="fa fa-clock-o"></i>&nbsp;
                                          {{ faveoDate($contractThreads->created_at) }}
                                         </span>
                                    </li> 
                                    @if($contractThreads->current)      
                                    <li>
                                        <i class="fa fa-paperclip bg-green">
                                    </i>
                                    @elseif($contractThreads->expired)
                                     <li>
                                        <i class="fa fa-paperclip bg-red">
                                     </i>
                                     @else
                                      <li>
                                        <i class="fa fa-paperclip bg-blue">
                                     </i>
                                    @endif
                                    <div class="timeline-item">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th>{{ Lang::get('ServiceDesk::lang.contract_start_date') }}</th>
                                                    <td> {{ faveoDate($contractThreads->contract_start_date)  }} </td>

                                                    <th>{{ Lang::get('ServiceDesk::lang.created_by') }}</th>
                                                    <td>  <a href="{{ url('user/'.$contractThreads->owner->id) }}"> {{ $contractThreads->owner->name  }} </a> </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ Lang::get('ServiceDesk::lang.contract_end_date') }}</th>
                                                    <td>  {{  faveoDate($contractThreads->contract_end_date)  }}  </td>
                                                    <th>{{ Lang::get('ServiceDesk::lang.approver') }}</th>
                                                    <td> <a href="{{ url('user/'.$contractThreads->approver->id) }}"> {{ $contractThreads->approver->name  }} </a></td>
                                                </tr>
                                                <tr>
                                                    <th>{{ Lang::get('ServiceDesk::lang.cost') }}</th>
                                                    <td> {{ $contractThreads->cost  }}  </td>
                                                    @if(array_key_exists('type',$contractThreads))
                                                    <td> <button type="button" class="btn btn-success btn-xs" style="pointer-events: none;"> {{ ucwords($contractThreads->type) }} </button> </td>
                                                    @endif
                                                </tr> 
                                            </tbody>
                                            </table>
                                        </div>
                                    </li>
                                    </ul>
                                @endforeach
                             @endif            
                            <!-- /.box-body -->
                           </div>
                         </div>
                                
                      </div>
                        <!-- /.box-body -->
                    </div>
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
        $('#problem-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! url("service-desk/get/attached/assets/". $contract->id."/sd_contracts") !!}',
            "lengthMenu": [[5, 10], [5, 10]],
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sSearch"    : "Search: ",
                "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink("image","gifloader3") !!}">'
            },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'managed_by', name: 'managed_by'},
                {data: 'used_by', name: 'used_by'},
                {data: 'action', name: 'action'},
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

<script>
        $('#notify-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! url("service-desk/get/notifiers/". $contract->id) !!}',
            "lengthMenu": [[5, 10], [5, 10]],
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sSearch"    : "Search: ",
                "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink("image","gifloader3") !!}">'
            },
            columns: [
                {data: 'name', name: 'name'},
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