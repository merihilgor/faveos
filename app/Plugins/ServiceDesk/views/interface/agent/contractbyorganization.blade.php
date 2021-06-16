
<head>
  <link href="{{assetLink('css','dataTables-bootstrap')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
</head>

  <div class="box box-primary">
    <div class="box-header with-border">
                <h3 class="box-title" title="{{$data->name}}">{!! Lang::get('ServiceDesk::lang.associated_contracts') !!} </h3>
            </div>   
            <div class="box-body">

                <table id="contract-table" class="display table table-bordered" cellspacing="0" width="100%">
                    <thead>
                       <tr>
                            <th>{!! Lang::get('ServiceDesk::lang.name') !!}</th>
                            <th>{!! Lang::get('ServiceDesk::lang.identifier') !!}</th>
                            <th>{!! Lang::get('ServiceDesk::lang.cost') !!}</th>
                            <th>{!! Lang::get('ServiceDesk::lang.expiry') !!}</th>
                       </tr>
                  </thead>
                </table>
            </div>
        </div>

 <script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
 <script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>

<script>
        $('#contract-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! url("service-desk/contract/organization/". $data->id) !!}',
            "lengthMenu": [[5, 10], [5, 10]],
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sSearch"    : "Search: ",
                "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
            },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'identifier', name: 'identifier'},
                {data: 'cost', name: 'cost'},
                {data: 'expiry', name: 'expiry'},
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