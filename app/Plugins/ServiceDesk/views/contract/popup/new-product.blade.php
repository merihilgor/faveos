<?php

  $sd_product_status = \App\Plugins\ServiceDesk\Model\Products\SdProductstatus::pluck('name', 'id')->toArray();
  $sd_product_proc_modes = \App\Plugins\ServiceDesk\Model\Products\SdProductprocmode::pluck('name', 'id')->toArray();
  $departments = \App\Plugins\ServiceDesk\Model\Assets\Department::pluck('name', 'id')->toArray();

?>

<a href="#new-product" data-toggle="modal" data-target="#new-product"><i class="fa fa-plus">&nbsp;&nbsp;</i>{{ Lang::get('ServiceDesk::lang.new') }} </a>

  
<!-- popup for show create new  product -->
<div class="modal fade" id="new-product">
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
                <h4 class="modal-title">{{ Lang::get('ServiceDesk::lang.create_new_product') }}</h4>
            </div>
          <div class="modal-body">


          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                <label for="name" class="control-label">{{Lang::get('ServiceDesk::lang.name')}} <span class="text-red"> *</span></label>
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    {!! Form::text('product-name as name',null,['class'=>'form-control','id'=>'pdt-name']) !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                <label class="control-label">{{Lang::get('ServiceDesk::lang.status')}}</label><span class="text-red"> *</span>
                <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                    <!--<div class="row">-->
                    <div class="col-md-6">
                        <p>{!! Form::radio('status', 1, true, ['id'=>'pdt-status']) !!} {{Lang::get('ServiceDesk::lang.enable')}}</p>
                    </div>
                    <div class="col-md-6">
                        <p>{!! Form::radio('status', 0, false, ['id'=>'pdt-status']) !!} {{Lang::get('ServiceDesk::lang.disable')}}</p>
                    </div>

                </div> 
            </div>  
        </div> 
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                <label for="manufacturer" class="control-label">{{Lang::get('ServiceDesk::lang.manufacturer')}}<span class="text-red"> *</span></label>

                <div class="form-group {{ $errors->has('manufacturer') ? 'has-error' : '' }}" >
                    {!! Form::text('product-manufacturer as manufacturer',null,['class'=>'form-control','id'=>'pdt-manufacturer']) !!}
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                <label class="control-label">{{Lang::get('ServiceDesk::lang.product_status')}}<span class="text-red"> *</span></label>
                <div class="form-group {{ $errors->has('Product_status') ? 'has-error' : '' }}">
                    {!! Form::select('Product_status',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.product_status')=>$sd_product_status],null,['class'=>'form-control','id'=> 'pdt-product-status']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                <label class=" control-label">{{Lang::get('ServiceDesk::lang.product_mode_procurement')}}</label><span class="text-red"> *</span></label>
                <div class="form-group {{ $errors->has('mode_procurement') ? 'has-error' : '' }}">
                    {!! Form::select('mode_procurement',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.procurement_modes')=>$sd_product_proc_modes],null,['class'=>'form-control', 'id' => 'pdt-mode-procurement']) !!}

                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                <label for="department_access" class="control-label">{{Lang::get('ServiceDesk::lang.department_access')}}</label><span class="text-red"> *</span></label>
                <div class="form-group {{ $errors->has('department_access') ? 'has-error' : '' }}">
                    {!! Form::select('department_access',[''=>Lang::get('ServiceDesk::lang.select'),Lang::get('ServiceDesk::lang.departments')=> $departments],null,['class'=>'form-control','id' => 'pdt-department-access']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
                <label for="description" class="control-label">{{Lang::get('ServiceDesk::lang.description')}} <span class="text-red"> *</span></label>
                <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}" >
                    {!! Form::textarea('product-description as description',null,['class'=>'form-control', 'id'=> 'pdt-description']) !!}
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <input type="button" value="submit" name="modalid1" class="btn btn-success" id="modalid1">
      </div>
      </div>
    </div>
</div>
    

<script type="text/javascript">

  $(document).on("click", "#modalid1", function() {
    
    $(".print-error-msg").find("ul").html('');
    var name = $('#pdt-name').val();
    var status = $('#pdt-status:checked').val();
    var manufacturer = $('#pdt-manufacturer').val();
    var Product_status = $('#pdt-product-status').val();
    var mode_procurement = $('#pdt-mode-procurement').val(); 
    var department_access = $('#pdt-department-access').val();
    var description = $('#pdt-description').val();
    var page = 1;

    $.ajax({

           type : 'post',
           url  : '{{ route("service-desk.post.products") }}',
           data : {
                   "_token": "{{ csrf_token() }}",
                    name : name, status : status,
                    manufacturer : manufacturer, Product_status : Product_status,
                    mode_procurement : mode_procurement, department_access : department_access,
                    description : description, page:page, 
                   },

                    success : function(result) {
                              $('#new-product').modal('show');
                              $('.success-msg').html(result);
                              $('.alert-success').css('display', 'block');
                              setInterval( function (){
                              $('.alert-success').slideUp(3000, function (){
                                });
                              }, 1500);
                              $('#pdt-name').val('').end();
                              $('#pdt-status').prop('checked', false);
                              $('#pdt-manufacturer').val('').end();
                              $('#pdt-product-status').val('').end();
                              $('#pdt-mode-procurement').val('').end();
                              $('#pdt-department-access').val('').end();
                              $('#pdt-description').val('').end();
                              setTimeout(function() {$('#new-product').modal('hide');}, 3000);
                              $("#productRefresh").load(" #productRefresh");
  
                    },
                    
                    error : function (result) {
                              var myJSON = JSON.parse(result.responseText);
                              for (var i in myJSON) {
                                    
                                     $(".print-error-msg").find("ul").append('<li>' + myJSON[i][0] + '</li>');

                                  }
                              $('#new-product').modal('show');
                              $('.alert-danger').css('display', 'block');  
                              setInterval( function (){
                                    $('.alert-danger').slideUp(7000, function(){
                                  });
                              }, 3000);       
                    },
          });
  });
  </script>

