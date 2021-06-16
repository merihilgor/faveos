<?php 
//$info = $problem->general();
$impact ="";
if ($problem->getGeneralByIdentifier('impact')) {
    $impact = $problem->getGeneralByIdentifier('impact')->value;
}
?>
<style>
    .left-hand{
        float: left;
    }
</style>
<div class="modal fade" id="impact{{$problem->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.impact')}}</h4>
                {!! Form::open(['url'=>'service-desk/general/'.$problem->id.'/sd_problem','files'=>true]) !!}
            </div>
            <div class="modal-body">
                <!-- Form  -->
                <div class="row">
                    <div class="col-md-12">
                       {!! Form::label('impact',Lang::get('ServiceDesk::lang.impact')) !!}
                       {!! Form::textarea('impact',$impact,['class'=>'form-control','id'=>'ckeditor1']) !!}
                       {!! Form::hidden('identifier','impact') !!}
                    </div>
                    <div class="col-md-12">
                       {!! Form::label('attachment',Lang::get('ServiceDesk::lang.attachment')) !!}
                      
                       {!! Form::file('attachment[]',['class'=>'file-data']) !!}
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default left-hand" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true">&nbsp;</i>{{Lang::get('ServiceDesk::lang.close')}}</button>
<!--                <input type="submit" class="btn btn-primary" value="{{Lang::get('lang.save')}}">-->
                    {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
                {!! Form::close() !!}
            </div>
            <!-- /Form -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
    $(function () {
        $("#impact").wysihtml5();
    });
</script>

<script type="text/javascript">
     if('{{Lang::getLocale()}}'=='ar'){
      setTimeout(function(){
         $('.left-hand').css('float','right');
    },100)
  }
</script>