<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title" title="{{$asset->name}}">Requests Associated with  {{str_limit(ucfirst($asset->name),20)}}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        {!! Datatable::table()
                    ->addColumn( 
                    Lang::get('ServiceDesk::lang.subject'),
                    Lang::get('ServiceDesk::lang.request'),
                    Lang::get('ServiceDesk::lang.status'),
                    Lang::get('ServiceDesk::lang.created')
                    )
                    ->setUrl(url('service-desk/assets/requesters?assetid='.$asset->id))  // this is the route where data will be retrieved
                    ->setOrder(array(3=>'desc'))
                    ->render() !!}
    </div>
    <!-- /.box-body -->
</div>