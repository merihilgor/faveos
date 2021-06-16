<link href="{{assetLink('css','perfect-scrollbar')}}" rel="stylesheet" type="text/css" >
<style>
.ps:hover > .ps__scrollbar-y-rail:hover {
    background-color: transparent !important;
    opacity: 0.9;
}
</style>

<center><a href="{{url('admin')}}"><li class="header"><span style="font-size:1.5em;">{{ Lang::get('lang.admin_panel') }}</span></li></a></center>
<li class="header">{!! Lang::get('lang.settings-2') !!}</li>
<li class="treeview">
    <a  href="#">
        <i class="fa fa-users"></i> <span>{!! Lang::get('lang.staffs') !!}</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li @yield('agents')><a href="{{ url('agents') }}" onclick="sidebaropen(this)"><i class="fa fa-user "></i>{!! Lang::get('lang.agents') !!}</a></li>
        <li @yield('departments')><a href="{{ url('departments') }}" onclick="sidebaropen(this)"><i class="fa fa-sitemap"></i>{!! Lang::get('lang.departments') !!}</a></li>
        <li @yield('teams')><a href="{{ url('teams') }}" onclick="sidebaropen(this)"><i class="fa fa-users"></i>{!! Lang::get('lang.teams') !!}</a></li>
        <li @yield('groups')><a href="{{ url('groups') }}" onclick="sidebaropen(this)"><i class="fa fa-users"></i>{!! Lang::get('lang.groups') !!}</a></li>
    </ul>
</li>

<li class="treeview">
    <a href="#">
        <i class="fa fa-envelope-o"></i>
        <span>{!! Lang::get('lang.email') !!}</span>
        <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li @yield('emails')><a href="{{ url('emails') }}" onclick="sidebaropen(this)"><i class="fa fa-envelope"></i>{!! Lang::get('lang.emails') !!}</a></li>
        <li @yield('ban')><a href="{{ url('banlist') }}" onclick="sidebaropen(this)"><i class="fa fa-ban"></i>{!! Lang::get('lang.ban_lists') !!}</a></li>
        <li @yield('template')><a href="{{ url('template-sets') }}" onclick="sidebaropen(this)"><i class="fa fa-mail-forward"></i>{!! Lang::get('lang.templates') !!}</a></li>
        <li @yield('email')><a href="{{url('getemail')}}" onclick="sidebaropen(this)"><i class="fa fa-at"></i>{!! Lang::get('lang.email-settings') !!}</a></li>
        <li @yield('diagnostics')><a href="{{ url('getdiagno') }}" onclick="sidebaropen(this)"><i class="fa fa-plus"></i>{!! Lang::get('lang.diagnostics') !!}</a></li>
        <!-- <li><a href="#"><i class="fa fa-circle-o"></i> Auto Response</a></li> -->
        <!-- <li><a href="#"><i class="fa fa-circle-o"></i> Rules/a></li> -->
        <!-- <li><a href="#"><i class="fa fa-circle-o"></i> Breaklines</a></li> -->
        <!-- <li><a href="#"><i class="fa fa-circle-o"></i> Log</a></li> -->
    </ul>
</li>

<li class="treeview">
    <a href="#">
        <i class="fa  fa-cubes"></i>
        <span>{!! Lang::get('lang.manage') !!}</span>
        <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li @yield('help')><a href="{{url('helptopic')}}" onclick="sidebaropen(this)"><i class="fa fa-file-text-o"></i>{!! Lang::get('lang.help_topics') !!}</a></li>
        <li @yield('sla')><a href="{{url('sla')}}" onclick="sidebaropen(this)"><i class="fa fa-clock-o"></i>{!! Lang::get('lang.sla_plans') !!}</a></li>
        <li @yield('forms')><a href="{{url('forms/create')}}" onclick="sidebaropen(this)"><i class="fa fa-file-text"></i>{!! Lang::get('lang.forms') !!}</a></li>
        <li @yield('workflow')><a href="{{url('workflow')}}" onclick="sidebaropen(this)"><i class="fa fa-sitemap"></i>{!! Lang::get('lang.workflow') !!}</a></li>
    </ul>
</li>

<li class="treeview">
    <a href="#" onclick="scroll2down(this)">
        <i class="fa fa-cog"></i>
        <span>{!! Lang::get('lang.settings') !!}</span>
        <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li @yield('company')><a href="{{url('getcompany')}}" onclick="sidebaropen(this)"><i class="fa fa-building"></i>{!! Lang::get('lang.company') !!}</a></li>
        <li @yield('system')><a href="{{url('getsystem')}}" onclick="sidebaropen(this)"><i class="fa fa-laptop"></i>{!! Lang::get('lang.system') !!}</a></li>
        <li @yield('tickets')><a href="{{url('getticket')}}" onclick="sidebaropen(this)"><i class="fa fa-file-text"></i>{!! Lang::get('lang.ticket') !!}</a></li>
        <li @yield('alert')><a href="{{url('alert')}}" onclick="sidebaropen(this)"><i class="fa fa-bell"></i>{!! Lang::get('lang.alert_notices') !!}</a></li>
        <li @yield('languages')><a href="{{url('languages')}}" onclick="sidebaropen(this)"><i class="fa fa-language"></i>{!! Lang::get('lang.language') !!}</a></li>
        <li @yield('cron')><a href="{{url('job-scheduler')}}" onclick="sidebaropen(this)"><i class="fa fa-hourglass"></i>{!! Lang::get('lang.cron') !!}</a></li>
        <li @yield('security')><a href="{{url('security')}}" onclick="sidebaropen(this)"><i class="fa fa-lock"></i>{!! Lang::get('lang.security') !!}</a></li>
        <li @yield('status')><a href="{{url('setting-status')}}" onclick="sidebaropen(this)"><i class="fa fa-plus-square-o"></i>{!! Lang::get('lang.status') !!}</a></li>
        <li @yield('notification')><a href="{{url('settings-notification')}}" onclick="sidebaropen(this)"><i class="fa fa-bell"></i>{!! Lang::get('lang.notifications') !!}</a></li>
        <li @yield('ratings')><a href="{{url('getratings')}}" onclick="sidebaropen(this)"><i class="fa fa-star"></i>{!! Lang::get('lang.ratings') !!}</a></li>
        <li @yield('close-workflow')><a href="{{url('close-workflow')}}" onclick="sidebaropen(this)"><i class="fa fa-sitemap"></i>{!! Lang::get('lang.close-workflow') !!}</a></li>
    </ul>
</li>
<li class="treeview">
    <a href="#">
        <i class="fa fa-heartbeat"></i>
        <span>{!! Lang::get('lang.error-debug') !!}</span>
        <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <!-- <li @yield('error-logs')><a href="{{ route('error.logs') }}"><i class="fa fa-list-alt"></i> {!! Lang::get('lang.view-logs') !!}</a></li> -->
        <li @yield('debugging-option')><a href="{{ route('err.debug.settings') }}" onclick="sidebaropen(this)"><i class="fa fa-bug"></i> {!! Lang::get('lang.debug-options') !!}</a></li>
    </ul>
</li>
<li class="treeview">
    <a href="#" onclick="scroll2down(this)">
        <i class="fa fa-pie-chart"></i>
        <span>{!! Lang::get('lang.widgets') !!}</span>
        <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li @yield('widget')><a href="{{ url('widgets') }}" onclick="sidebaropen(this)"><i class="fa fa-list-alt"></i> {!! Lang::get('lang.widgets') !!}</a></li>
        <li @yield('socail')><a href="{{ url('social-buttons') }}" onclick="sidebaropen(this)"><i class="fa fa-cubes"></i> {!! Lang::get('lang.social') !!}</a></li>
    </ul>
</li>
<li class="treeview">
    <a href="{{ url('plugins') }}">
        <i class="fa fa-plug"></i>
        <span>{!! Lang::get('lang.plugin') !!}</span>
    </a>
</li>
<li class="treeview">
    <a href="{{ url('api') }}">
        <i class="fa fa-cogs"></i>
       <span>{!! Lang::get('lang.api') !!}</span>
    </a>
</li>
<li class="treeview">
    <a href="#" onclick="scroll2down(this)">
        <i class="fa fa-sitemap"></i> <span>{{Lang::get('ServiceDesk::lang.service_desk')}}</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li class=""><a href="{{url('service-desk/products')}}" onclick="sidebaropen(this)"><i class="fa fa-industry"></i>{{Lang::get('ServiceDesk::lang.products')}}</a></li>
        <li class=""><a href="{{url('service-desk/procurement')}}" onclick="sidebaropen(this)"><i class="fa fa-phone"></i>{{Lang::get('ServiceDesk::lang.procurement_types')}}</a></li>
        <li class=""><a href="{{url('service-desk/contract-types')}}" onclick="sidebaropen(this)"><i class="fa fa-paper-plane"></i>{{Lang::get('ServiceDesk::lang.contract_types')}}</a></li>
        <li class=""><a href="{{url('service-desk/license-types')}}" onclick="sidebaropen(this)"><i class="fa fa-paste"></i>{{Lang::get('ServiceDesk::lang.license_type')}}</a></li>
        <li class=""><a href="{{url('service-desk/vendor')}}" onclick="sidebaropen(this)"><i class="fa fa-barcode"></i>{{Lang::get('ServiceDesk::lang.vendors')}}</a></li>
         <li class=""><a href="{{url('service-desk/assetstypes')}}" onclick="sidebaropen(this)"><i class="fa fa-briefcase"></i>{{Lang::get('ServiceDesk::lang.asset_types')}}</a></li>
        <li class=""><a href="{{url('service-desk/cabs')}}" onclick="sidebaropen(this)"><i class="fa fa-users"></i>{{Lang::get('ServiceDesk::lang.cabs')}}</a></li>
        <li class=""><a href="{{url('service-desk/announcement')}}" onclick="sidebaropen(this)"><i class="fa fa-volume-down" aria-hidden="true"></i>{{Lang::get('ServiceDesk::lang.announcement-page-title')}}</a></li>

    </ul>
</li>
<script src="{{assetLink('js','perfect-scrollbar')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
    var container = document.getElementById('sideMenu');
    Ps.initialize(container);
setTimeout(function(){
    $('.slimScrollDiv').removeAttr('style');
    $('#sideMenu').removeAttr('style');
},2000)
})
</script>

--}}
@push('scripts')

@endpush