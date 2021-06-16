<?php
 $createdAt =  faveoDate($problem['created_at']);
 $subject = ($problem['subject'] != null)? $problem['subject']:null;
 $description = strip_tags($problem['description']);
 $problemId = $problem['id'];
 $departmentArray = $problem['department'];
 $department = ($problem['department'] != null)? $departmentArray['name']:null;
 $impactArray = $problem['impact_id'];
 $impact =  ($problem['impact_id'] != null)? $impactArray['name']:null;
 $statusArray = $problem['status_type_id'];
 $status = ($problem['status_type_id'] != null) ? $statusArray['name']:null;
 $priorityArray = $problem['priority_id'];
 $priority = ($problem['priority_id'] != null) ? $priorityArray['priority']:null;
 $sdPolicy = new App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy();
?>

<head>
  <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
</head>

<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">{{ Lang::get('ServiceDesk::lang.associated_assets') }}</a></li>
                <li><a href="#tab_2" data-toggle="tab">{{ Lang::get('ServiceDesk::lang.associated_problem') }}</a></li>
            </ul>
              
            <div class="tab-content">
                <!-- /.tab-pane 1-->
                <div class="tab-pane active" id="tab_1">
                    <div class="box box-primary">
                        <div class="box-body">
                          
                          <table id="asset-table" class="table display" cellspacing="0" width="100%" styleClass="borderless">
                               <thead><tr>
                                        <th>{!! Lang::get('ServiceDesk::lang.name') !!}</th>
                                        <th>{!! Lang::get('ServiceDesk::lang.managed_by') !!}</th>
                                        <th>{!! Lang::get('ServiceDesk::lang.used_by') !!}</th>
                                        <th>{!! Lang::get('ServiceDesk::lang.action') !!}</th>
                                    </tr>
                                </thead>
                         </table>
                       </div>
                    </div>
                </div>
                <!-- /.tab-pane2 -->

            <div class="tab-pane" id="tab_2">
                <div class="box box-primary">
                    <div class="box-body">
                   @if($problem['id'] != null)
                      <div class="tab-pane active" id="timeline">
                       <!-- The timeline -->
                        <ul class="timeline timeline-inverse">
                          <!-- timeline time label -->
                          <li class="time-label">

                                <span class="bg-green"><i class="fa fa-clock-o"></i>&nbsp;
                                 {!! $createdAt !!}
                                </span>
                          </li>

                        <li>
                          <i class="fa fa-envelope bg-blue"></i>
                           <div class="timeline-item">
                           <h3 class="timeline-header no-border"><a style="pointer-events: none">{{ Lang::get('ServiceDesk::lang.problem_subject') }}</a> </h3>
                           <table class="table table-bordered">
                        <tbody>
                        <tr>
                           <td><b>#PRB-{{ $problem['id'] }}</b> &nbsp;&nbsp;{!!  $subject !!}</td>
                        </tr>
                         </tbody>
                        </table>
                        </div>
                       </li>

                        <li>
                          <i class="fa fa-info-circle bg-aqua"></i>
                           <div class="timeline-item">
                           <h3 class="timeline-header no-border"><a style="pointer-events: none">{{ Lang::get('ServiceDesk::lang.problem_details') }}</a> </h3>
                           <table class="table table-bordered">
                        <tbody>
                        <tr>
                          <th>{{ Lang::get('ServiceDesk::lang.department') }}</th>
                          <td>{!! $department !!}</td>
                          <th>{{ Lang::get('ServiceDesk::lang.priority') }}</th>
                          <td>{!! $priority !!}</td>
                        </tr>
                        <tr>
                           <th>{{ Lang::get('ServiceDesk::lang.status') }}</th>
                           <td>{!! $status !!}</td>
                           <th>{{ Lang::get('ServiceDesk::lang.impact') }}</th>
                           <td>{!! $impact !!}</td>
                         </tr>
                         </tbody>
                        </table>
                        </div>
                       </li>
                          <!-- timeline item -->
                          <li>
                               <i class="fa fa-bug bg-blue"></i>
                               <div class="timeline-item">
                               <span class="time"></span>
                               <h3 class="timeline-header"><a style="pointer-events:none;">{{Lang::get('ServiceDesk::lang.problem_description')}}</a></h3>
                               <div class="timeline-body">
                                {{ str_limit($description,400) }}
                               </div>
                               <div class="timeline-footer">
                               @if($sdPolicy->problemsView())
                               <a  href="{{url('service-desk/problem/'.$problemId.'/show')}}" class="btn btn-primary btn-xs"><i class="fa fa-eye">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.view')}}</a>&nbsp;
                               @endif
                               @if($sdPolicy->problemDetach())
                               <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#addpopup{!!  $problemId !!}" onclick="proper()"><i class="fa fa-trash" aria-hidden="true">&nbsp;&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.detach')}}</button>
                               @endif
                               </div>
                               </div>
                           </li>
                            <!-- timeline item --> 
                       @else
                       {{Lang::get('ServiceDesk::lang.no_data_available')}}
                       @endif
                        </div>
                        <!-- /.box-body -->
                        </div>
                    <!-- /.box -->

                </div>
                <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- nav-tabs-custom -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<div class="modal fade" id="addpopup{!! $problemId !!}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.detach')}}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p>{{Lang::get('ServiceDesk::lang.are_you_sure')}}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close">&nbsp;</i>{{Lang::get('ServiceDesk::lang.close')}}</button>
                <a href="{{ url('service-desk/problem/detach/' . $ticketid.'/'.$problemId) }}"  onclick="clickAndDisable(this);" class="btn btn-danger"><i class="fa fa-trash">&nbsp;</i>{{Lang::get('ServiceDesk::lang.detach')}}</a>
            </div>
        </div>
    </div>
</div>

<script> 
   function clickAndDisable(link) {
     // disable subsequent clicks
     link.onclick = function(event) {
        event.preventDefault();
     }
   }   
</script>
 <script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
 <script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
<script>
        $('#asset-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! url("service-desk/attached/assets/ticket/".$ticketid) !!}',
            "lengthMenu": [[5, 10], [5, 10]],
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sSearch"    : "Search: ",
                "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
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
