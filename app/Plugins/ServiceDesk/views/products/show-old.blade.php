@extends('themes.default1.admin.layout.admin')
@section('content')

<section class="content-heading-anchor">
    <h2>
        {{Lang::get('ServiceDesk::lang.show-product')}}  


    </h2>

</section>


<!-- Main content -->

<div class="box box-primary">
    <div class="box-header with-border">
        <h4> 
            {{ucfirst($product->name)}}  
            <a href="{{url('service-desk/products/'.$product->id.'/edit')}}" class="btn btn-default">{{Lang::get('ServiceDesk::lang.edit')}}</a>
        </h4>
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- fail message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{{Lang::get('message.alert')}}!</b> {{Lang::get('message.failed')}}.
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">


                <!-- /.box-header -->

                <table class="table table-condensed">

                    <tr>

                        <td>{{Lang::get('ServiceDesk::lang.name')}}</td>
                        <td>
                            {!!$product->name!!}
                        </td>

                    </tr>
                    <tr>
                        <td>{{Lang::get('ServiceDesk::lang.status')}}</td>
                        <td>
                            {!!$product->statuses()!!}
                        </td>
                    </tr>

                    <tr>
                        <td>{{Lang::get('ServiceDesk::lang.manufacturer')}}</td>
                        <td>
                            {!!$product->manufacturer!!}
                        </td>
                    </tr>
                    <tr>
                        <td>{{Lang::get('ServiceDesk::lang.product_status')}}</td>
                        <td>
                            {!!$product->productStatuses()!!}
                        </td>
                    </tr>
                    <tr>
                        <td>{{Lang::get('ServiceDesk::lang.product_mode_procurement')}}</td>
                        <td>
                            {!!$product->procurements()!!}
                        </td>
                    </tr>
                    <tr>
                        <td>{{Lang::get('ServiceDesk::lang.department_access')}}</td>
                        <td>
                            {!!$product->departments()!!}
                        </td>
                    </tr>
                    <tr>
                        <td>{{Lang::get('ServiceDesk::lang.description')}}</td>
                        <td>
                            {!!$product->descriptions()!!}
                        </td>
                    </tr>
                    
                    
                </table>
            </div>
            <div class="col-md-12">
                <h3>{{Lang::get('ServiceDesk::lang.attachment')}}</h3>
            </div>
            @forelse($product->attachments() as $attachment)
            <div class="col-md-3">
                {{$attachment->value}}
            </div>
            @empty 
            <div class="col-md-12">
                <p>No Attachments</p>
            </div>
            @endforelse

        </div>
        <!-- /.box -->
    </div>
</div>


@stop
