<style>
    .table{
        width: 100% !important; 
    },
    .left-hand{
        float: left;
    }
</style>
<div class="modal fade" id="changeold{{$problem->id}}">
    {!! Form::open(['url'=>'service-desk/problem/change/attach/'.$problem->id,'files'=>true]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.changes')}}</h4>
            </div>
            <div class="modal-body">
               <table id="changelist" class="table display" cellspacing="0" width="100%" styleClass="borderless">
                    <thead><tr>
                      <th>#</th>
                      <th>{!! Lang::get('ServiceDesk::lang.changes') !!}</th>
                      </tr></thead>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default left-hand" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true">&nbsp;</i>{{Lang::get('ServiceDesk::lang.close')}}</button>
                   {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.attach'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
                
            </div>
            
            <!-- /Form -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    {!! Form::close() !!}
</div><!-- /.modal -->
<script src="{{assetLink('js','jquery-dataTables')}}" type="text/javascript"></script>
<script src="{{assetLink('js','dataTables-bootstrap')}}"  type="text/javascript"></script>
<script>
    $('#changelist').DataTable({
            processing: true,
            serverSide: true,
            bDestroy: true,
            ajax: "{!! url('service-desk/problem/getChanges') !!}",
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sSearch"    : "Search: ",
                "sProcessing": '<img id="blur-bg" class="backgroundfadein" style="top:40%;left:50%; width: 50px; height:50 px; display: block; position:    fixed;" src="{!! assetLink('image','gifloader3') !!}">'
            },
               
            columns: [
                {data: 'id', name: 'id'},
                {data: 'subject', name: 'subject'},
            ],
            "fnDrawCallback": function( oSettings ) {
                $('.loader').css('display', 'none');
            },
            "fnPreDrawCallback": function(oSettings, json) {
                $('.loader').css('display', 'block');
            },
        });
  
     if('{{Lang::getLocale()}}'=='ar'){
      setTimeout(function(){
         $('.left-hand').css('float','right');
    },100)
  }
</script>