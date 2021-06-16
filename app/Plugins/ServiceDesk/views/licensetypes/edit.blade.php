@extends('themes.default1.admin.layout.admin')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        
        ?>
@section('meta-tags')
<meta name="title" content="{{Lang::get('ServiceDesk::lang.license_types_edit-page-title')}}  :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{{Lang::get('ServiceDesk::lang.license_types_edit-page-description')}} ">

@stop

@section('PageHeader')
    <h1>{{Lang::get('ServiceDesk::lang.edit_license_type')}}</h1>
@stop


@section('content')
    <div id="app-sevicedesk">
        <license-type-create-edit></license-type-create-edit>
    </div>

    <script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop