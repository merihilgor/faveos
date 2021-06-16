
<head>
  <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
</head>

<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">{{ Lang::get('ServiceDesk::lang.associated_assets') }}</a></li>

            </ul>
            <div class="tab-content">
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
 <script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
 <script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>


 <script>
        $('#asset-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! url("service-desk/get/attached/assets/". $release->id."/sd_releases") !!}',
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
