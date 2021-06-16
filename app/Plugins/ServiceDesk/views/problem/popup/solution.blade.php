<?php 
//$info = $problem->general();
$title ="";
$solution = "";
if ($problem->getGeneralByIdentifier('solution-title')) {
    $title = $problem->getGeneralByIdentifier('solution-title')->value;
}
if ($problem->getGeneralByIdentifier('solution')) {
    $solution = $problem->getGeneralByIdentifier('solution')->value;
}
?>
<style>
    .left-hand{
        float: left;
    }
</style>
<div class="modal fade" id="solution{{$problem->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.solution')}}</h4>
                {!! Form::open(['url'=>'service-desk/general/'.$problem->id.'/sd_problem','files'=>true]) !!}
            </div>
            <div class="modal-body">
                <!-- Form  -->
                <div class="row">
                    <div class="col-md-12">
                       {!! Form::label('solution-title',Lang::get('ServiceDesk::lang.solution_title')) !!}
                       {!! Form::text('solution-title',$title,['class'=>'form-control']) !!}
                       {!! Form::hidden('identifier','solution') !!}
                    </div>
                    <div class="col-md-12">
                       {!! Form::label('solution',Lang::get('ServiceDesk::lang.solution')) !!}
                       {!! Form::textarea('solution',$solution,['class'=>'form-control','id'=>'myNicEditor']) !!}
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
 $(".box-primary").on('change',".file-data", function(){
  
            if(this.files[0].size > 2048*1024){
                $(this).parent().find(".file-error").empty()
                $(this).parent().append("<p class='file-error' style='color:red'>cannot upload files more than 2 MB</p>")
                $(this).val('');
            }
        else{
            $(this).parent().find(".file-error").empty()
        }
   });
 </script>
<script type="text/javascript">
     if('{{Lang::getLocale()}}'=='ar'){
      setTimeout(function(){
         $('.left-hand').css('float','right');
    },100)
  }
</script>
