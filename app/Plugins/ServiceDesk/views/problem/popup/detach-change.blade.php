<div class="modal fade" id="change-detach{{$problem->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.detach')}} </h4>
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
                <a href="{{url('service-desk/problem/'.$problem->id.'/detach')}}" class="btn btn-danger"  onclick="clickAndDisable(this);"><i class="fa fa-trash">&nbsp;</i>{{Lang::get('ServiceDesk::lang.detach')}}</a>
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