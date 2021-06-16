<style>
    .table .table-bordered {
        width: 100%     !important;
    },
     .left-hand{
        float: left;
    }
</style>

<a href="#attachAsset" class="btn btn-info btn-xs" data-toggle="modal" data-target="#attachAsset"><i class="fa fa-server">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.attach_asset')}}</a>

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
                        <?php
                        $types = App\Plugins\ServiceDesk\Model\Assets\SdAssettypes::pluck('name', 'id')->toArray();
                        ?>
                         <label class=" control-label">{{Lang::get('ServiceDesk::lang.asset_type')}}</label>&nbsp;&nbsp;&nbsp;<a href="#" data-toggle="tooltip" title="{!! Lang::get('ServiceDesk::lang.select_asset_based_on_asset-types') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></a> 
                        {!! Form::select('asset_type',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.choose')=>$types],null,['class'=>'form-control','id'=>'asset_type']) !!}
                    </div>
                    <div class="col-md-12">
                        {!! Form::open(['url'=>'service-desk/asset/attach/releases']) !!}
                        {!! Form::hidden('releaseid',$release->id) !!}

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
                <button type="button"  class="btn btn-default left-hand" data-dismiss="modal"><i class="fa fa-times">&nbsp;</i>{!! Lang::get('ServiceDesk::lang.close') !!}</button>
                <!-- <input type="submit" class="btn btn-primary" value="{{Lang::get('lang.save')}}"> -->
                 {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
            </div>

            {!! Form::close() !!}
            <!-- /Form -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->  
 <script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
 <script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
<script>
     document.getElementById('asset_type').onchange = function () {
       var dataId =$('#asset_type').val();
    $('#asset-attach-table1').DataTable({
            processing: true,
            serverSide: true,
            bDestroy: true,
            ajax: "{!! url('service-desk/asset-type/') !!}"+'/'+dataId,
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sSearch"    : "Search: ",
                "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
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
  
    $(document).ready(function () { /// Wait till page is loaded
            $('#click').click(function () {
                $('#refresh').load('open #refresh');
                $("#show").show();
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
