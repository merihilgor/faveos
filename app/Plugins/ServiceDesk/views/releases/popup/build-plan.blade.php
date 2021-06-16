<?php
$build="";
if ($release->getGeneralByIdentifier('build-plan')) {
    $build = $release->getGeneralByIdentifier('build-plan')->value;
}
?>
<style>
    .left-hand{
        float: left;
    }
</style>
<div class="modal fade" id="build-plan{{$release->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.build_plan')}}</h4>
                {!! Form::open(['url'=>'service-desk/general/'.$release->id.'/sd_releases','files'=>true]) !!}
            </div>
            <div class="modal-body">
                <!-- Form  -->
                <div class="row">
                    
                    <div class="col-md-12">
                       {!! Form::hidden('identifier','build-plan') !!}
                       {!! Form::label('build-plan',Lang::get('ServiceDesk::lang.build_plan')) !!}
                       {!! Form::textarea('build-plan',$build,['class'=>'form-control','id'=>'build-plan']) !!}
                    </div>
                     <div class="col-md-12">
                       {!! Form::label('attachment',Lang::get('ServiceDesk::lang.attachment')) !!}
                    {!! Form::file('attachment[]',['class'=>'file-data']) !!}
                    </div>
                    
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default left-hand" data-dismiss="modal"><i class="fa fa-times">&nbsp;</i>{{Lang::get('ServiceDesk::lang.close')}}</button>
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

 <script>
    $(function(){
           CKEDITOR.replace('build-plan', {
              toolbarGroups: [
                {"name": "basicstyles", "groups": ["basicstyles"]},
                {"name": "links", "groups": ["links"]},
                {"name": "paragraph", "groups": ["list", "blocks"]},
                {"name": "document", "groups": ["mode"]},
                {"name": "insert", "groups": ["insert"]},
                {"name": "styles", "groups": ["styles"]},
                {"name": "colors", "groups": ["TextColor", "BGColor"]}
               ],
            removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar',
            disableNativeSpellChecker: false
            }).on('change', function() { 
                     $('#submit').removeAttr('disabled');
            });
            CKEDITOR.config.scayt_autoStartup = true;
            CKEDITOR.config.extraPlugins = 'font,bidi,colorbutton,autolink,colordialog';
            CKEDITOR.config.width = '100%';
            CKEDITOR.config.menu_groups = 
    'tablecell,tablecellproperties,tablerow,tablecolumn,table,' +
    'anchor,link,image,flash';
    })
   function proper(){
       setTimeout(function(){
          
        $(".cke_wysiwyg_frame").contents().find("body").css('word-wrap','break-word');
       },2000);
   }
</script>
<script type="text/javascript">
     if('{{Lang::getLocale()}}'=='ar'){
      setTimeout(function(){
         $('.left-hand').css('float','right');
    },100)
  }
</script>
