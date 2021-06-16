
 <a class="btn btn-danger btn-xs" data-toggle="modal" data-target="#reject"><i class="fa fa-unlink">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.reject')}}</a>


<!-- popup for show reject in view page -->
<div class="modal fade" id="reject">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.reason_rejection')}}</h4>
            </div>
            {!! Form::open(['url'=>'service-desk/api/contract-reject/'.$contract->id,'method'=>'post']) !!}
            <div class="modal-body">
                <!-- Form  -->
                <div class="row">         
                    <div class="col-md-12">
                       {!! Form::textarea('purpose_of_rejection',null,['class'=>'form-control','id'=>'ckeditor2']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                  {!!Form::button('<i class="fa fa-unlink" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.reject'),['type' => 'submit', 'class' =>'btn btn-danger'])!!} 
            </div>
            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>