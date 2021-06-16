<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">{{Lang::get('ServiceDesk::lang.vendors')}}</h3>
        <div class="pull-right">
            <div class="btn-group">

                <a href="#add" data-toggle="modal" class="btn btn-primary btn-xs" data-target="#add{{$product->id}}" id="modal1"><i class="fa fa-plus">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.new')}}</a>

            </div>&nbsp;
            <div class="btn-group">

                <a href="#existing" data-toggle="modal" class="btn btn-primary btn-xs" data-target="#existing{{$product->id}}"><i class="fa fa-plus">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.existing')}}</a>

            </div>
        </div>
    </div>
    <!--/.box-header--> 
    <div class="box-body">
        <table class="table table-condensed">
            <tr>
                <th>#</th>
                <th>{{Lang::get('ServiceDesk::lang.name')}}</th>
                <th>{{Lang::get('ServiceDesk::lang.email')}}</th>
                <th>{{Lang::get('ServiceDesk::lang.contact')}}</th>
                <th>{{Lang::get('ServiceDesk::lang.action')}}</th>
            </tr>

            <?php
            $i = 1;
            ?>
            @forelse($product->vendors() as $vendor)
            <tr>
                <td>{{$i}}</td>
                <td><a href="{{url('service-desk/vendor/'.$vendor->id.'/show')}}" title="{{$vendor->name}}">{{str_limit(($vendor->name),20)}}</a></td>
                <td>{{$vendor->email}}</td>
                <td>{{$vendor->primary_contact}}</td>
                <td>
                    <div class="btn-group">
                        <a href="{{url('service-desk/vendor/'.$vendor->id.'/edit')}}" class="btn btn-primary btn-xs"><i class="fa fa-edit">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.edit')}}</a>
                    </div>&nbsp;
                    <div class="btn-group">
                        <?php
                        $url = url('service-desk/products/' . $product->id . '/remove/' . $vendor->id . '/vendor');
                        $delete = \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::deletePopUp($vendor->id, $url, "Delete $vendor->name", "btn btn-primary btn-xs");
                        ?>
                        {!! $delete !!}
                    </div>
                    <div class="btn-group">

                       <a href="{{url('service-desk/vendor/'.$vendor->id.'/show')}}" class="btn btn-primary btn-xs"><i class="fa fa-eye">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.view')}}</a>
                    </div>    
                </td>
            </tr>
            <?php $i++; ?>
            @empty 
            <tr><td>No vendor Associated</td></tr>
            @endforelse

        </table>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->
@include('service::products.popup.addnew-vendor')
@include('service::products.popup.addexisting-vendor')
<script>
    $(document).ready(function(){
        
        if($('.alert-success').html()){
              
                setInterval(function(){
                    $('.alert-success').slideUp( 3000, function() {});
                }, 2000);
            }
        
    });
</script>
<script>
$(document).on("click", "#modal1", function () {
   
    $('#submit').attr('disabled','disabled');
    });
    $('#Form').on('input',function(){
        
        $('#submit').removeAttr('disabled');
    })
    $('#Form').on('change',':input',function(){
       
        $('#submit').removeAttr('disabled');
    })
    
    
</script>
          