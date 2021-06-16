@extends('themes.default1.agent.layout.agent')
<?php
        $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        if (isset($title->name)) {
            $title_name = $title->name;
        } else {
            $title_name = "SUPPORT CENTER";
        }
        ?>

    <?php
        try {
            
            $authController = new App\Http\Controllers\Auth\AuthController();
            // auth user data
            $authInfo = $authController->getLoggedInClientInfo();
        } catch(\Exception $e) {
            // ignore exception
        }
    ?>
@section('meta-tags')
<meta name="title" content="{{Lang::get('ServiceDesk::lang.contracts_view-page-title')}}  :: {!! strip_tags($title_name) !!} ">
<meta name="description" content="{{Lang::get('ServiceDesk::lang.contracts_view-page-description')}} ">

@stop

@section('custom-css')
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
@stop

@section('PageHeader')
    <h1> {{Lang::get('ServiceDesk::lang.contract')}} </h1>
@stop

<script src="{{assetLink('js','vue-ckeditor')}}" type="text/javascript" async></script>

@section('content')
    
    <div id="app-sevicedesk">

        <contract-view :auth-info="{{ json_encode($authInfo) }}"></contract-view>
    </div>
    
    <script src="{{ asset('js/serviceDesk.js') }}"></script>
@stop