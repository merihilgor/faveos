<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>


<!--<a href="#impact" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#impact{{$problem->id}}">Impact</a>-->
<?php
$statuses = \App\Plugins\ServiceDesk\Model\Changes\SdChangestatus::pluck('name', 'id')->toArray();
$sd_changes_priorities = \App\Plugins\ServiceDesk\Model\Changes\SdChangepriorities::pluck('name', 'id')->toArray();
$sd_changes_types = App\Plugins\ServiceDesk\Model\Changes\SdChangetypes::pluck('name', 'id')->toArray();
$sd_impact_types = App\Plugins\ServiceDesk\Model\Assets\SdImpactypes::pluck('name', 'id')->toArray();
//$sd_locations = \App\Plugins\ServiceDesk\Model\Releases\SdLocations::pluck('title', 'id')->toArray();
 $location=App\Location\Models\Location::pluck('title','id')->toArray();//for dropdown showing all location
$assets = \App\Plugins\ServiceDesk\Model\Assets\SdAssets::pluck('name', 'id')->toArray();

$requester = App\User::where('role', 'agent')->orWhere('role', 'admin')->pluck('email', 'id')->toArray();
?>
<style type="text/css">
    .left-hand{
        float: left;
    }
</style>
<div class="modal fade" id="changenew{{$problem->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.change')}}</h4>
                {!! Form::open(['url'=>'service-desk/problem/change/'.$problem->id,'method'=>'post','files'=>true]) !!}
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 {{ $errors->has('subject') ? 'has-error' : '' }}">
                        <label for="inputPassword3" class="control-label">{{Lang::get('ServiceDesk::lang.subject')}}<span class="text-red"> *</span></label> </label>
                        {!! Form::text('subject',null,['class'=>'form-control','required']) !!}
                        <!--<input type="text" class="form-control" name="subject" id="inputPassword3" placeholder="Subject">-->
                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('requester') ? 'has-error' : '' }}">
                        <label for="inputPassword3" class="control-label">{{Lang::get('ServiceDesk::lang.requester')}}<span class="text-red"> *</span></label>
                       <!--  {!! Form::select('requester',$requester,null,['class'=>'form-control']) !!} -->
                        <!--<input type="text" class="form-control" name="subject" id="inputPassword3" placeholder="Subject">-->
                        
                          {!!Form::select('requester_id',[Lang::get('lang.requester')=>''],null,['class' => 'form-control select2','id'=>'assign-requesters','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}

                     </div>

                    <div class="form-group col-md-12 {{ $errors->has('description') ? 'has-error' : '' }}">
                        <label for="internal_notes" class="control-label" >{{Lang::get('ServiceDesk::lang.description')}}<span class="text-red"> *</span></label>
                        {!! Form::textarea('description',null,['class'=>'form-control','required']) !!}
                        <!--<textarea class="form-control textarea" name="description" rows="7" id="internal_notes"></textarea>-->
                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('status_id') ? 'has-error' : '' }}">
                        <label class=" control-label">{{Lang::get('ServiceDesk::lang.status')}}</label><span class="text-red"> *</span>
                        {!! Form::select('status_id',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.statuses')=>$statuses],null,['class'=>'form-control','required'])!!}
                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('priority_id') ? 'has-error' : '' }}">
                        <label class="control-label">{{Lang::get('ServiceDesk::lang.priority')}}</label><span class="text-red"> *</span>
                        {!! Form::select('priority_id',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.priorities')=>$sd_changes_priorities],null,['class'=>'form-control','required']) !!}

                    </div> 
                    <div class="form-group col-md-6 {{ $errors->has('change_type_id') ? 'has-error' : '' }}">
                        <label class=" control-label">{{Lang::get('ServiceDesk::lang.change_type')}}</label><span class="text-red"> *</span>
                        {!! Form::select('change_type_id',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.types')=>$sd_changes_types],null,['class'=>'form-control','required']) !!}

                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('location_id') ? 'has-error' : '' }}">
                        <label class="control-label">{{Lang::get('ServiceDesk::lang.location')}}</label>
                        {!! Form::select('location_id',[''=>Lang::get('ServiceDesk::lang.select')]+$location,null,['class'=>'form-control']) !!}

                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('impact_id') ? 'has-error' : '' }}">
                        <label class=" control-label" >{{Lang::get('ServiceDesk::lang.impact_type')}}</label><span class="text-red"> *</span>
                        {!! Form::select('impact_id',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.impact_types')=>$sd_impact_types],null,['class'=>'form-control','required']) !!}

                    </div>
                    <div class="form-group col-md-6 {{ $errors->has('asset') ? 'has-error' : '' }}">
                        <label class=" control-label" >{{Lang::get('ServiceDesk::lang.attach_asset')}}</label>
                        {!!Form::select('asset[]',[Lang::get('ServiceDesk::lang.asset')=>''],null,['class' => 'form-control select2','id'=>'assetlist','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}
                   </div>
                    <div class="form-group col-md-6 {{ $errors->has('attachments') ? 'has-error' : '' }}">
                        <label class=" control-label" >{{Lang::get('ServiceDesk::lang.attachment')}}</label>
                   {!! Form::file('attachments[]',['class'=>'file-data']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default left-hand" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true">&nbsp;</i>{{Lang::get('ServiceDesk::lang.close')}}</button>
<!--                <input type="submit" class="btn btn-primary" value="{{Lang::get('lang.save')}}">-->
                   {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.attach'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
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
 <!-- For auto search  -->
 <script src="{{assetLink('js','select2')}}"></script>
 <script>
    $('#assign-requesters').select2({
       
        maximumSelectionLength: 1,
        minimumInputLength: 1,
        ajax: {

            url: '{{url("ticket/form/requester?type=requester")}}',

            dataType: 'json',
            data: function(params) {
                // alert(params);
                return {
                    term: $.trim(params.term)
                };
            },
             processResults: function(data) {
                return{
                 results: $.map(data, function (value) {
                    return {
                        image:value.profile_pic,
                        text:value.first_name+" "+value.last_name,
                        id:value.id,
                        email:value.email,
                    }
                })
               }
            },
            cache: true
        },
         templateResult: formatState,
    });
   function formatState (state) { 
       
       var $state = $( '<div><div style="width: 20%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 78%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
        return $state;
  }
</script>
<script>
    $('#assetlist').select2({
       
        maximumSelectionLength: 10,
        minimumInputLength: 1,
        ajax: {

            url: '{{url("service-desk/get/assetlist")}}',

            dataType: 'json',
            data: function(params) {
                // alert(params);
                return {
                    term: $.trim(params.term)
                };
            },
             processResults: function(data) {
                return{
                 results: $.map(data, function (value) {
                    return {
                        text:value.name,
                        id:value.id,
                    }
                })
               }
            },
            cache: true
        },
    });
   
</script>
