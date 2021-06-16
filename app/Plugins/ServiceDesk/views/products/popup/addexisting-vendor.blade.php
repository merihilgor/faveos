@section('custom-css')
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
@stop

<style>
    .left-hand{
        float: left;
    }
</style>
<div class="modal fade" id="existing{{$product->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.vendor')}}</h4>
                {!! Form::open(['url'=>'service-desk/products/add-existing/vendor','method'=>'post']) !!}
                {!! Form::hidden('product',$product->id) !!}
            </div>
            <div class="modal-body">
                <!-- <?php 
                    $vendor = new App\Plugins\ServiceDesk\Model\Vendor\SdVendors();
                    $this_vendors  = $product->vendorRelation()->pluck('vendor_id')->toArray();;
                    $vendors = $vendor->whereNotIn('id',$this_vendors)->pluck('name','id')->toArray();
                ?>
                {!! Form::select('vendor',$vendors,null,['class'=>'form-control']) !!} -->

                {!!Form::select('vendor',[Lang::get('lang.vendor')=>''],null,['class' => 'form-control select2','id'=>'vendor-api','style'=>'width:100%;display: block; max-height: 200px; overflow-y: auto;','multiple'=>'true']) !!}

          </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default left-hand" data-dismiss="modal"><i class="fa fa-times">&nbsp;</i>{{Lang::get('ServiceDesk::lang.close')}}</button>
<!--                <input type="submit" class="btn btn-primary" value="{{Lang::get('lang.save')}}">-->
                     {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- For auto search for vendor  -->
 <script src="{{assetLink('js','select2')}}"></script>
 <script>

    $('#vendor-api').select2({
       
        maximumSelectionLength: 1,
        minimumInputLength: 1,
        ajax: {
            url: '{{url("service-desk/vendor/api")}}',
            dataType: 'json',
            data: function(params) {
                //alert(params);
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
         //templateResult: formatState,
    });
</script>
<script type="text/javascript">
     if('{{Lang::getLocale()}}'=='ar'){
      setTimeout(function(){
         $('.left-hand').css('float','right');
    },100)
  }
</script>

@endpush