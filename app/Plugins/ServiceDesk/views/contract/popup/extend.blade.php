@section('custom-css')
<link href="{{assetLink('css','bootstrap-datetimepicker4')}}" rel="stylesheet" type="text/css" />
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
@stop

<?php
// dd(date('Y-m-d H:i:s', strtotime($contract->contract_end_date . '+1 day')));
  $start = ($contract->contract_start_date != "--") ? faveoDate($contract->contract_start_date) : null;
  $end   = faveoDate(date('Y-m-d H:i:s', strtotime($end)));
?>


<a href="#extend" class="btn btn-success btn-xs" data-toggle="modal" data-target="#extend"><i class="fa fa-retweet">&nbsp;&nbsp;</i>{{ Lang::get('ServiceDesk::lang.extend') }} </a>

<!-- popup for show renew details in view page -->
<div class="modal fade" id="extend">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ Lang::get('ServiceDesk::lang.extend_contract') }}</h4>
            </div>
            <div class="modal-body">

            {!! Form::open(['url'=>"service-desk/api/contract-extend/$contract->id",'method'=>'post','files'=>true]) !!}
             
                <div class="form-group col-md-6 {{ $errors->has('contract_start_date') ? 'has-error' : '' }}">
                    <label for="start" class="control-label">{{Lang::get('ServiceDesk::lang.contract_start_date')}}<span class="text-red"> *</span></label>
                    {!! Form::text('contract_start_date',$end,['class'=>'form-control','id'=>'datetimepicker8', 'disabled']) !!}
                </div>

                <div class="form-group col-md-6 {{ $errors->has('contract_end_date') ? 'has-error' : '' }}">
                    <label for="end date" class="control-label">{{Lang::get('ServiceDesk::lang.contract_end_date')}}<span class="text-red"> *</span></label>
                    {!! Form::text('contract_end_date',$end,['class'=>'form-control','id'=>'datetimepicker9','required']) !!}
                </div>

                <div class="form-group col-md-6 {{ $errors->has('approver_id') ? 'has-error' : '' }}">
                    <label class=" control-label">{{Lang::get('ServiceDesk::lang.approver')}}<span class="text-red"> *</span></label>    
                    {!!Form::select('approver_id',[Lang::get('lang.approver')=>''],null,['class' => 'form-control select2','id'=>'approver-id','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true','required']) !!}
                </div>

                <div class="form-group col-md-6 {{ $errors->has('Cost') ? 'has-error' : '' }}">
                    <label for="inputEmail3" class="control-label">{{Lang::get('ServiceDesk::lang.cost')}}<span class="text-red"> *</span></label>
                    {!! Form::number('cost',null,['class'=>'form-control','required']) !!}
                </div>

                <div class="col-md-6 {{ $errors->has('notify_expiry') ? 'has-error' : '' }}">
                  <label for="inputEmail3" class="control-label">{{Lang::get('ServiceDesk::lang.notify_before')}}</label>&nbsp;<span data-toggle="tooltip" style="color:#3c8dbc;" title="{!! Lang::get('ServiceDesk::lang.notify_info') !!}"><i class="fa fa-question-circle" style="padding: 0px;"></i></span> 
                  {!! Form::number('notify_before',null,['class'=>'form-control']) !!}
                </div>

                <div class="form-group col-md-6 {{ $errors->has('agent_ids') ? 'has-error' : '' }}">
                  <label class=" control-label">{{Lang::get('ServiceDesk::lang.notify_agents')}}</label>    
                  {!!Form::select('agent_ids[]',[Lang::get('lang.notify_agents')=>''],null,['class' => 'form-control select2','id'=>'agent_ids','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default left-hand" data-dismiss="modal" id="dismis2"><i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.close')}}</button>
                {!!Form::button('<i class="fa fa-retweet" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.extend'),['type' => 'submit', 'class' =>'btn btn-success'])!!}
            {!! Form::close() !!}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<?php
    $dateTimeFormat = dateTimeFormat(true); 
    
    $end = $end ? $end : 0;
?>

<!--  approver  -->
<script src="{{assetLink('js','select2')}}"></script>
<script>
    $(function () {
      $('#approver-id').select2({

        maximumSelectionLength: 1,
        minimumInputLength: 2,
        ajax: {

            url: '{{url("ticket/form/requester?type=agent")}}',

            dataType: 'json',
            data: function(params) {
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

         var $state = $( '<div><div style="width: 8%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 90%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
         return $state;
     }
 });
</script> 
<script src="{{assetLink('js','moment')}}" type="text/javascript"></script>
<script src="{{assetLink('js','bootstrap-datetimepicker4')}}" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {


        $('#datetimepicker8').datetimepicker({format: '{{ $dateTimeFormat }}'});
        $('#datetimepicker9').datetimepicker({minDate : '{{ $end }}', format: '{{ $dateTimeFormat }}',
            useCurrent: false //Important! See issue #1075
        });
        $("#datetimepicker8").on("dp.change", function (e) {
            $('#datetimepicker9').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker8").on("dp.change", function (e) {
            $('#datetimepicker9').data("DateTimePicker").maxDate(e.date);
        });
    });
</script>

<script type="text/javascript">
   $(function() {
      
      $('#agent_ids').select2({

        maximumSelectionLength: 30,
        minimumInputLength: 2,
        ajax: {
            
            url: '{{url("ticket/form/requester?type=agent")}}',
            dataType: 'json',

            data: function(params) {
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

         var $state = $( '<div><div style="width: 8%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 90%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
         return $state;
     }
 });
</script>   

