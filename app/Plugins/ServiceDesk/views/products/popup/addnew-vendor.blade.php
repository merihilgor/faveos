<style>
    .left-hand{
        float: left;
    }
</style>
<div class="modal fade" id="add{{$product->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['url'=>'service-desk/products/add/vendor','method'=>'post','id'=>'Form']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Lang::get('ServiceDesk::lang.vendor')}}</h4>
                
                {!! Form::hidden('product',$product->id) !!}
            </div>
            <div class="modal-body">
               <div class="row">


            <div class="form-group">


                <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6 form-group">
                    <label for="name" class="control-label">{{Lang::get('ServiceDesk::lang.name')}} <span class="text-red"> *</span></label>
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        {!! Form::text('name',null,['class'=>'form-control','required']) !!}
                        <!--<input type="text" name="name" class="form-control" id="inputPassword3" placeholder="Name">-->
                    </div>
                </div>

                <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6 form-group">
                    <label for="inputEmail3" class="control-label">{{Lang::get('ServiceDesk::lang.email')}}<span class="text-red"> *</span></label>
                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                        {!! Form::email('email',null,['class'=>'form-control','required']) !!}
                        <!--<input type="email" class="form-control" name="email" placeholder="Email">-->
                    </div>
                </div>
            </div>


            <div class="form-group">


                <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6 form-group">
                    <label for="name" class="control-label">{{Lang::get('ServiceDesk::lang.primary_contact')}}<span class="text-red"> *</span></label>
                    <div class="form-group {{ $errors->has('primary_contact') ? 'has-error' : '' }}">
                        {!! Form::number('primary_contact',null,['class'=>'form-control','maxlength'=>'20','required']) !!}
                       
                    </div>
                </div>

                <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6 form-group">
                    <label for="inputEmail3" class="control-label">{{Lang::get('ServiceDesk::lang.address')}}<span class="text-red"> *</span></label>
                    <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                        {{ Form::textarea('address', null, ['class' => 'form-control','required','rows' => 2, 'cols' => 40,'style'=>'resize: vertical']) }}
                        <!--<input type="text" class="form-control" name="address" placeholder="Address">-->
                    </div>
                </div>
            </div>


            <div class="form-group">


                <div class="col-md-3 form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                {!! Form::label('status',Lang::get('lang.status')) !!}
                <div class="row">
                    <div class="col-xs-4">
                        {!! Form::radio('status','1',true) !!} {{Lang::get('ServiceDesk::lang.active')}}
                    </div>
                    <div class="col-xs-6">
                        {!! Form::radio('status','0') !!} {{Lang::get('ServiceDesk::lang.inactive')}}
                    </div>
                </div>
            </div>


        

                <!--            <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6 form-group">
                                <label for="inputEmail3" class="control-label">{{Lang::get('ServiceDesk::lang.all_department')}}</label>
                                <div class="form-group {{ $errors->has('all_department') ? 'has-error' : '' }}">
                                    {!! Form::text('all_department',null,['class'=>'form-control']) !!}
                                    <input type="text" class="form-control" name="all_department" placeholder="All Department">
                                </div>
                            </div>-->
            </div>

            <div class="form-group">
                <div class="col-xs-11 col-md-11 col-sm-11 col-lg-11">
                    <label for="internal_notes" class="control-label">{{Lang::get('ServiceDesk::lang.description')}}</label>
                    <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                        {!! Form::textarea('description',null,['class'=>'form-control']) !!}
                        <!--<textarea class="form-control textarea" name="description" rows="7" id="" placeholder="description"></textarea>-->
                    </div>
                </div>
            </div>
        </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default left-hand" data-dismiss="modal"><i class="fa fa-times">&nbsp;</i>{{Lang::get('ServiceDesk::lang.close')}}</button>
<!--                <input type="submit" class="btn btn-primary" id="submit" value="{{Lang::get('lang.save')}}">-->
                   {!!Form::button('<i class="fa fa-floppy-o" aria-hidden="true">&nbsp;&nbsp;</i>'.Lang::get('ServiceDesk::lang.save'),['type' => 'submit', 'class' =>'btn btn-primary'])!!}
                
               {!! Form::close() !!}
            </div>
            <!-- /Form -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
     if('{{Lang::getLocale()}}'=='ar'){
      setTimeout(function(){
         $('.left-hand').css('float','right');
    },100)
  }
</script>