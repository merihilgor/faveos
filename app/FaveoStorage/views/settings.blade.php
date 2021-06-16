@extends('themes.default1.admin.layout.admin')

@section('Settings')
active
@stop

@section('settings-bar')
active
@stop

@section('storage')
class="active"
@stop

@section('PageHeader')
<h1>{{ Lang::get('storage::lang.storage')}}</h1>
@stop

@section('HeadInclude')
@stop
@section('content')
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
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
<div class="box box-primary">
{!! Form::open(['url'=>'storage','method'=>'post','id'=>'Form']) !!}
    <div class="box-header with-border">
        <h4> {{Lang::get('storage::lang.storage')}} </h4>
      
        
    </div><!-- /.box-header -->
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div id="root">
                <div class="form-group col-md-6 {{ $errors->has('private-root') ? 'has-error' : '' }}">
                    {!! Form::label('storage-path',Lang::get('storage::lang.storage-path')) !!}
                    {!! Form::text('private-root',$private_folder,['class'=>'form-control']) !!}             
                </div>
            </div>
        </div>
        

</div>
<!-- /.box-body -->
</div>
<div class="box-footer">

    <button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'>&nbsp;</i> Saving..."><i class="fa fa-floppy-o">&nbsp;&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
 
{!! Form::close() !!}
</div>
<!-- /.box -->
</div>
@stop
@section('FooterInclude')

@stop