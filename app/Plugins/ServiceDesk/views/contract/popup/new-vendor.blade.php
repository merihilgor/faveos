

<a href="#new-vendor"  data-toggle="modal" data-target="#new-vendor"><i class="fa fa-plus">&nbsp;&nbsp;</i>{{ Lang::get('ServiceDesk::lang.new') }} </a>


<div class="modal fade" id="new-vendor">
    <div class="modal-dialog">
        <div class="modal-content" id="vendorRefresh">
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
                  <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.new-vendor')}}</h4>
            </div>

            <div class="modal-body">
            
            <div class="row">
              <div class="form-group">
                <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6 form-group">
                    <label for="name" class="control-label">{{Lang::get('ServiceDesk::lang.name')}}<span class="text-red"> *</span></label>
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        {!! Form::text('vendor-name as name',null,['class'=>'form-control', 'id'=>'vname']) !!}
                    </div>
                </div>
                <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6 form-group">
                    <label for="inputEmail3" class="control-label">{{Lang::get('ServiceDesk::lang.email')}}<span class="text-red"> *</span></label>
                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                        {!! Form::email('email',null,['class'=>'form-control', 'id'=>'vemail']) !!}
                    </div>
                </div>
              </div>
  
              <div class="form-group">
                <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6 form-group">
                    <label for="name" class="control-label">{{Lang::get('ServiceDesk::lang.primary_contact')}}<span class="text-red"> *</span></label>
                    <div class="form-group {{ $errors->has('primary_contact') ? 'has-error' : '' }}">
                        {!! Form::number('primary_contact',null,['class'=>'form-control','min'=>'0', 'id'=>'vprimary_contact']) !!}
                    </div>
                </div>
                <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6 form-group">
                    <label for="inputEmail3" class="control-label">{{Lang::get('ServiceDesk::lang.address')}}<span class="text-red"> *</span></label>
                    <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                        {{ Form::textarea('address', null, ['class' => 'form-control','rows' => 2, 'cols' => 40,'style'=>'resize: vertical', 'id'=>'vaddress']) }}
                    </div>
                </div>
              </div>

              <div class="form-group">
                  <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 form-group">
                    <label for="name" class="control-label">{{Lang::get('ServiceDesk::lang.status')}}</label>
                     <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-md-2">
                                <p>{!! Form::radio('status', 1, true, ['id'=>'vstatus']) !!} {{Lang::get('ServiceDesk::lang.active')}}</p>
                            </div>
                            <div class="col-md-2">
                                <p>{!! Form::radio('status', 0, false, ['id'=>'vstatus']) !!} {{Lang::get('ServiceDesk::lang.inactive')}}</p>
                            </div>
                        </div>
                      </div>
                  </div>
               </div>

              <div class="form-group">
                  <div class="form-group col-md-12">
                     <label for="internal_notes" class="control-label">{{Lang::get('ServiceDesk::lang.description')}}</label>
                     <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                        {!! Form::textarea('vendor-description as description',null,['class'=>'form-control' , 'id'=>'vdescription']) !!}
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
             <input type ="button" name ="modelid2" value ="submit" id ="modelid2" class= "btn btn-success">
          </div>
        </div>
    </div>
</div>

<script type="text/javascript">

  $(document).on("click", "#modelid2", function (){
      
      $(".print-error-msg").find("ul").html("");
      var name = $('#vname').val();
      var email = $("#vemail").val();
      var primary_contact = $("#vprimary_contact").val();
      var address = $("#vaddress").val();
      var status = $("#vstatus:checked").val();
      var description = $("#vdescription").val();
      var page = 1;

      $.ajax({

             type : "post",
             url  : '{{ route("service-desk.vendor.postcreate") }}',
             data : {
                    "_token": "{{ csrf_token() }}",
                    name : name, email : email,
                    primary_contact : primary_contact, address : address,
                    status : status, description : description, page : page,
                },

                success : function(result) {
                          $("#new-vendor").modal('show');
                          $(".success-msg").html(result);
                          $(".alert-success").css("display", "block");
                          setInterval( function(){
                          $(".alert-success").slideUp(3000, function(){
                            });
                          },1500);
                          $('#vname').val('').end();
                          $("#vemail").val('').end();
                          $("#vprimary_contact").val('').end();
                          $("#vaddress").val('').end();
                          $("#vstatus").val('').end();
                          $("#vdescription").val('').end();
                          setTimeout(function() {$('#new-vendor').modal('hide');}, 3000);
                          $("#vendorRefresh").load(" #vendorRefresh");

                },

                error : function(result) {
                        var myJson = JSON.parse(result.responseText);
                        for(var i in myJson){
                          
                             $(".print-error-msg").find("ul").append("<li>"+ myJson[i][0] +"</li>");
                        }
                        $("#new-vendor").modal("show");
                        $(".alert-danger").css("display", "block");
                        setInterval ( function(){
                        $(".alert-danger").slideUp(7000, function(){    
                        });
                        },3000);

                },
            });
  });

</script>