

<a href="#contract-type" data-toggle="modal" data-target="#contract-type"><i class="fa fa-plus">&nbsp;&nbsp;</i>{{ Lang::get('ServiceDesk::lang.new') }} </a>

  
<!-- popup for show create new contract-type -->
<div class="modal fade" id="contract-type">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
           <div class="alert alert-success alert-dismissable" style="display: none;">
              <i class="fa  fa-check-circle"></i>
              <span class="success-msg"></span>
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          </div>
          <div class="alert alert-danger alert-dismissable print-error-msg" style="display:none;margin-right:15px;margin-left:15px;">
               <i class="fa fa-ban"></i>
                  <strong>{{Lang::get('ServiceDesk::lang.whoops')}}</strong>&nbsp;{{Lang::get('ServiceDesk::lang.there_were_some_problems_with_your_input')}} <br><br>
               <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
               <ul></ul>
          </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ Lang::get('ServiceDesk::lang.create_new_contract_type') }}</h4>
          </div>
          <div class="modal-body">
                  <div class="row">
                     <div class="form-group col-md-6 {{ $errors->has('nf') ? 'has-error' : '' }} ">
                      <label class="control-label">{{Lang::get('ServiceDesk::lang.name')}}<span class="text-red"> *</span></label>
                      {!! Form::text('contract-type-name as name',null,['class'=>'form-control', 'id'=>'contract-types']) !!}
                      <span id="error" style="color:red;"> <span>
                     </div>
                  </div>
            </div>
            <div class="modal-footer">
                <input type="button" value="submit" name="x" class="btn btn-success" id="modalid">
            </div>
      </div>
    </div>
</div>
    

<script type="text/javascript">
      
       $(document).on("click","#modalid",function() {
        $(".print-error-msg").find("ul").html('');
        var name = $('#contract-types').val();
        var page = 1;
         
          $.ajax({
                   type: 'post',
                   url: '{{route("service-desk.contractstypes.create")}}',
                   data: {
                            "_token": "{{ csrf_token() }}",
                            name: name,page:page,
                        },
                            success: function(result) {

                            $('#contract-type').modal('show');
                            $('.success-msg').html(result);
                            $('.alert-success').css('display', 'block');
                            setInterval(function() {
                            $('.alert-success').slideUp(3000, function() {
                            });
                            }, 1500);
                            $('#contract-types').val('').end();
                            setTimeout(function() {$('#contract-type').modal('hide');}, 3000);
                            $("#thisdiv").load(" #thisdiv");

                            },

                            error: function(result) {

                             var myJSON = JSON.parse(result.responseText);
                              for (var i in myJSON) {
                                    
                                     $(".print-error-msg").find("ul").append('<li>' + myJSON[i][0] + '</li>');

                                  }
                              $('#contract-type').modal('show');
                              $('.alert-danger').css('display', 'block');  
                              setInterval( function (){
                                    $('.alert-danger').slideUp(7000, function(){
                                  });
                              }, 3000);       

                            },

                });
          });
</script>

 
