@extends('themes.default1.admin.layout.admin')

@section('PageHeader')
    <h1> {{trans('lang.form-group')}} </h1>
@stop

@section('content')

<div>
  <div id="app-admin">
    <!-- <form-builder :getmenu="'ticket'"></form-builder> -->
    <form-builder-main
      :form-name-obj="{ id: 'name' , className: 'col-md-6', label: 'form_group_name', placeholder: 'name_your_form_group', value: '', submitFormParamKey: 'name' }"
      :form-linker="[{ id: 'asset_types', className: 'col-md-6', apiEndpoint: 'service-desk/api/dependency/asset_types?config=true', label: 'asset_type', value: [], submitFormParamKey: 'asset_type_ids' }]"
      post-from-group-api="service-desk/api/post-form-group"
      edit-form-group-api="service-desk/api/get-form-group"
      form-category-type="asset">
    </form-builder-main>
  </div>

  <script src="{{asset('js/admin.js')}}" type="text/javascript"></script>
  
</div>

@stop