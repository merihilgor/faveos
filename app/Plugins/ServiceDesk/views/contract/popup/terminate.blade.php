
<a class="btn btn-danger btn-xs" data-toggle="modal" data-target="#terminate"><i class="fa fa-stop">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.terminate')}}</a>


<!-- popup for show terminate in view page -->
<div class="modal fade" id="terminate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.terminate')}}</h4>
            </div>
            <div class="modal-body">
               {{ Lang::get('ServiceDesk::lang.terminate_description') }}
            </div>
            <div class="modal-footer">
                {!! Form::open(['url'=>"service-desk/api/contract-terminate/$contract->id",'method'=>'post','files'=>true]) !!}
                {!!Form::button('<i class="fa fa-forward" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.continue'),['type' => 'submit', 'class' =>'btn btn-danger right-hand'])!!}     
                <button type="button" class="btn btn-default left-hand" data-dismiss="modal" id="dismis2"><i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.close')}}</button>
                {!! Form::close() !!}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>