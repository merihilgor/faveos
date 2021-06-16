<?php
    $sdPolicy = new App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy();
?>
<ul class="nav navbar-nav navbar-left"> 
@if(config('app.name')!= 'Faveo Helpdesk Community')
<li @yield('Report')><a href="{{URL::route('report.get')}}" onclick="clickReport(event);"><i class="fa fa-line-chart" aria-hidden="true">&nbsp;</i>{!! Lang::get('ServiceDesk::lang.reports') !!}</a></li>
@endif
   @if($sdPolicy->problemsView())
    <li>
        <a href="{{url('service-desk/problems')}}" onclick="sidebaropen(this)">
            <i class="fa fa-bug"></i> 
            <span style="">{!! Lang::get('ServiceDesk::lang.problems') !!}</span> 
        </a>
    </li>
    @endif
    @if($sdPolicy->changesView())
    <li>
        <a href="{{url('service-desk/changes')}}" onclick="sidebaropen(this)">
            <i class="fa fa-refresh"></i> 
            <span style="">{!! Lang::get('ServiceDesk::lang.changes') !!}</span> 
        </a>
    </li> 
    @endif
@if($sdPolicy->releasesView())
@if(Lang::getLocale() == "ar")
    <li>
        <a href="{{url('service-desk/releases')}}" onclick="sidebaropen(this)" style="margin-top: -7px">
            <i class="fa fa-newspaper-o"></i> 
            <span style="">{!! Lang::get('ServiceDesk::lang.releases') !!}</span> 
        </a>
    </li>
@else
     <li>
        <a href="{{url('service-desk/releases')}}" onclick="sidebaropen(this)">
            <i class="fa fa-newspaper-o"></i> 
            <span style="">{!! Lang::get('ServiceDesk::lang.releases') !!}</span> 
        </a>
    </li>
@endif 
@endif
@if($sdPolicy->assetsView())
    <li>

        <a href="{{url('service-desk/assets')}}" onclick="sidebaropen(this)">
            <i class="fa fa-server"></i> 
            <span style=""> {!! Lang::get('ServiceDesk::lang.assets') !!}</span> 
        </a>

    </li>
@endif
<!--    <li>
        <a href="{{url('service-desk/contracts')}}">
            <i class="fa fa-paperclip"></i> 
            <span style=""> Contracts</span> 
        </a>


    </li>-->
    @if($sdPolicy->problemCreate() && $sdPolicy->problemsView()  || $sdPolicy->changesView() && $sdPolicy->changeCreate() || $sdPolicy->releasesView() && $sdPolicy->releaseCreate() || $sdPolicy->assetsView() && $sdPolicy->assetCreate() || $sdPolicy->contractsView() && $sdPolicy->contractCreate())

    <li class="hidden-xs dropdown" id="newdiv">
        <a class="dropdown-toggle" data-toggle="dropdown" title="{!! Lang::get('ServiceDesk::lang.new') !!}">
            <i class="glyphicon glyphicon-plus" style="font-size:10px;"></i> {!! Lang::get('ServiceDesk::lang.new') !!}<span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
        @if($sdPolicy->problemsView())
            @if($sdPolicy->problemCreate())
            <li>
                <a href="{{url('service-desk/problem/create')}}" onclick='topbaropen("{{url('service-desk/problems')}}")'>{!! Lang::get('ServiceDesk::lang.problems') !!}</a>
            </li>
            @endif
        @endif

        @if($sdPolicy->changesView())
            @if($sdPolicy->changeCreate())
            <li>
                <a href="{{url('service-desk/changes/create')}}" onclick='topbaropen("{{url('service-desk/changes')}}")'>{!! Lang::get('ServiceDesk::lang.changes') !!}</a>
            </li>
            @endif
        @endif

        @if($sdPolicy->releasesView())
            @if($sdPolicy->releaseCreate())
            <li>
                <a href="{{url('service-desk/releases/create')}}" onclick='topbaropen("{{url('service-desk/releases')}}")'>{!! Lang::get('ServiceDesk::lang.releases') !!}</a>
            </li>
            @endif
        @endif
        
        @if($sdPolicy->assetsView())
            @if($sdPolicy->assetCreate())
            <li>
                <a href="{{url('service-desk/assets/create')}}" onclick='topbaropen("{{url('service-desk/assets')}}")'>{!! Lang::get('ServiceDesk::lang.assets') !!}</a>
            </li>
            @endif
        @endif
        
        @if($sdPolicy->contractsView())
            @if($sdPolicy->contractCreate())
            <li>
                <a href="{{url('service-desk/contracts/create')}}">Contracts</a>
            </li>
            @endif
        @endif
        </ul>
    </li>

   @endif

    @if(isPlugin('Calendar'))
       <?php \Event::dispatch('calendar.topbar', array()); ?>
    @endif
</ul>