<?php
    $sdPolicy = new App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy();
?>
<link href="{{assetLink('css','perfect-scrollbar')}}" rel="stylesheet" type="text/css" >
<style>
.ps:hover > .ps__scrollbar-y-rail:hover {
    background-color: transparent !important;
    opacity: 0.9;
}
</style>

<li class="treeview">
    <a href="{{url('dashboard')}}">
        <i class="fa fa-dashboard"></i> 
        <span style="margin-left:-2%;">{!! Lang::get('lang.dashboard') !!}</span> 
    </a>
</li>
<li class="treeview">
    <a href="#">
        <i class="fa fa-user"></i> <span>{!! Lang::get('lang.tickets') !!}</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu" >
        @if($ticket_policy->create())
        <li @yield('newticket')>
             <a href="{{ url('/newticket')}}" onclick="sidebaropen(this)">
                <i class="fa fa-ticket"></i>{!! Lang::get('lang.create_ticket') !!}
            </a>
        </li>
        @endif
        <li @yield('inbox')>
             <a href="{{ url('/tickets')}}" id="load-inbox" onclick="sidebaropen(this)">
                <i class="fa fa-inbox"></i> <span>{!! Lang::get('lang.inbox') !!}</span> <small class="label pull-right bg-green">{{$tickets -> count()}}</small>                                            
            </a>
        </li>
        <li @yield('myticket')>
             <a href="{{ url('/tickets?show=mytickets')}}" id="load-myticket" onclick="sidebaropen(this)">
                <i class="fa fa-user"></i> <span>{!! Lang::get('lang.my_tickets') !!} </span>
                <small class="label pull-right bg-green">{{$myticket -> count()}}</small>
            </a>
        </li>
        <li @yield('unassigned')>
            <?php 
               $version = (\Cache::has('inbox-layout')) ? \Cache::get('inbox-layout') : 'new';
               $url = ($version == 'new') ? url("/tickets?show=unassigned"): url("/tickets?assigned[]=0");
            ?>
             <a href="{{ $url }}" id="load-unassigned" onclick="sidebaropen(this)">
                <i class="fa fa-user-times"></i> <span>{!! Lang::get('lang.unassigned') !!}</span>
                <small class="label pull-right bg-green">{{$unassigned -> count()}}</small>
            </a>
        </li>
        <li @yield('overdue')>
             <a href="{{url('/tickets?show=overdue')}}" id="load-unassigned" onclick="sidebaropen(this)">
                <i class="fa fa-calendar-times-o"></i> <span>{!! Lang::get('lang.overdue') !!}</span>
                <small class="label pull-right bg-green">{{$overdues -> count()}}</small>
            </a>
        </li>
              @if($ticket_policy->viewUnapprovedTickets())
                            <li @yield('unapproved')>
                                 <a href="{{url('/tickets?show=unapproved')}}" id="load-unapproved">
                                    <i class="fa fa-calendar-times-o"></i> <span>{!! Lang::get('lang.unapproved') !!}</span>
                                    <small class="label pull-right bg-green">{{$unapproved -> count()}}</small>
                                </a>
                            </li>
                        @endif
                        
                        @if($approval_enable->first()->status == 1)
                        <?php
                        $is_team_lead = 0;
                        $is_department_manager = 0;
                        if (\Auth::user()->role == 'admin') {
                            $is_team_lead = 1;
                            $is_department_manager = 1;
                        } else {
                            $is_team_lead = \DB::table('teams')->where('team_lead', '=', \Auth::user()->id)->count();
                            $is_department_manager = \DB::table('department')->where('manager', '=', \Auth::user()->id)->count();
                        }
                        ?>
                        @if($is_team_lead == 1 || $is_department_manager == 1)
                        <li @yield('approval')>
                            <a href="{{url('/tickets?show=approval')}}" id="load-unassigned">
                                <i class="fa fa fa-bell"></i> <span>{!! Lang::get('lang.approval') !!}</span>
                                <small class="label pull-right bg-green">{{$closingapproval -> count()}}</small>
                            </a>
                        </li>
                        @endif
                        @endif

                         <li @yield('waiting_for_approval')>
                             <a href="{{url('/tickets?show=waiting-for-approval')}}" id="load-waiting-for-approval">
                                <i class="fa fa-clock-o"></i> <span>{!! Lang::get('lang.waiting_for_approval') !!}</span>
                                <small class="label pull-right bg-green">{{$waiting_for_approval->count()}}</small>
                            </a>
                         </li>
                         
        <li @yield('closed')>
             <a href="{{ url('/tickets?show=closed')}}" onclick="sidebaropen(this)">
                <i class="fa fa-minus-circle"></i> <span>{!! Lang::get('lang.closed') !!}</span>
                <small class="label pull-right bg-green">{{$closed -> count()}}</small>
            </a>
        </li>
        <li @yield('trash')>
             <a href="{{ url('/tickets?show=trash')}}" onclick="sidebaropen(this)">
                <i class="fa fa-trash-o"></i> <span>{!! Lang::get('lang.trash') !!}</span>
                <small class="label pull-right bg-green">{{$deleted -> count()}}</small>
            </a>
        </li>

    </ul>
</li>
<li class="treeview">
    <a href="#">
        <i class="fa fa-users"></i> <span>{{Lang::get('lang.users')}}</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{url('user')}}"  onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i>{{Lang::get('lang.user_directory')}}</a></li>
        <li class=""><a href="{{url('organizations')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i>{{Lang::get('lang.organization')}}</a></li>
        <li class=""><a href="{{url('user-export')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i>{{Lang::get('lang.export_user')}}</a></li>
    </ul>
</li>





<li class="treeview">
    <a href="#">
        <i class="fa fa-wrench"></i> <span>{{Lang::get('lang.tools')}}</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{url('canned/list')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i>{{Lang::get('lang.canned_response')}}</a></li>
        <li class="treeview @yield('category')">
            <a href="#">
                <i class="fa fa-list-ul"></i> <span>{{Lang::get('lang.category')}}</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li @yield('add-category')><a href="{{url('category/create')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('lang.addcategory')}}</a></li>
                <li @yield('all-category')><a href="{{url('category')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('lang.allcategory')}}</a></li>
            </ul>
        </li>
        <li class="treeview @yield('article')">
            <a href="#">
                <i class="fa fa-edit"></i> <span>{{Lang::get('lang.article')}}</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li @yield('add-article')><a href="{{url('article/create')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('lang.addarticle')}}</a></li>
                <li @yield('all-article')><a href="{{url('article')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('lang.allarticle')}}</a></li>
            </ul>
        </li>
        <li class="treeview @yield('pages')">
            <a href="#">
                <i class="fa fa-file-text"></i> <span>{{Lang::get('lang.pages')}}</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li @yield('add-pages')><a href="{{url('page/create')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('lang.addpages')}}</a></li>
                <li @yield('all-pages')><a href="{{url('page')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('lang.allpages')}}</a></li>
            </ul>
        </li>

        <li @yield('comment')>
             <a href="{{url('comment')}}" onclick="sidebaropen(this)">
                <i class="fa fa-comments-o"></i>
                <span>{{Lang::get('lang.comments')}}</span>
            </a>
        </li>
        <li @yield('settings')>
             <a href="{{url('kb/settings')}}" onclick="sidebaropen(this)">
                <i class="fa fa-wrench"></i>
                <span>{{Lang::get('lang.settings')}}</span>
            </a>
        </li>
    </ul>
</li>

@if($sdPolicy->problemsView())
<li class="treeview">
    <a href="#">
        <i class="fa fa-bug"></i> <span>{{Lang::get('ServiceDesk::lang.problems')}}</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{url('service-desk/problems')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('ServiceDesk::lang.all_problem')}}</a></li>
    @if($sdPolicy->problemCreate())
        <li class=""><a href="{{url('service-desk/problem/create')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i>{{Lang::get('ServiceDesk::lang.new_problem')}} </a></li>
    @endif

    </ul>
</li>
@endif

@if($sdPolicy->changesView())
<li class="treeview">
    <a href="#">
        <i class="fa fa-refresh"></i> <span>{{Lang::get('ServiceDesk::lang.changes')}}</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{url('service-desk/changes')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('ServiceDesk::lang.all_changes')}}</a></li>
    @if($sdPolicy->changeCreate())
        <li class=""><a href="{{url('service-desk/changes/create')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i>{{Lang::get('ServiceDesk::lang.new_changes')}} </a></li>
    @endif
    </ul>
</li>
@endif

@if($sdPolicy->releasesView())
<li class="treeview">
    <a href="#">
        <i class="fa fa-newspaper-o"></i> <span>{{Lang::get('ServiceDesk::lang.releases')}}</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{url('service-desk/releases')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('ServiceDesk::lang.all_releases')}}</a></li>
    @if($sdPolicy->releaseCreate())
        <li class=""><a href="{{url('service-desk/releases/create')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i>{{Lang::get('ServiceDesk::lang.new_releases')}} </a></li>
    @endif
    </ul>
</li>
@endif
@if($sdPolicy->assetsView())
<li class="treeview">
    <a href="#">
        <i class="fa fa-server"></i> <span>{{Lang::get('ServiceDesk::lang.assets')}}</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{url('service-desk/assets')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('ServiceDesk::lang.all_assets')}}</a></li>
        @if($sdPolicy->assetCreate())
        <li class=""><a href="{{url('service-desk/assets/create')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i>{{Lang::get('ServiceDesk::lang.new_assets')}} </a></li>
        @endif
        <li><a href="{{url('service-desk/assets/export')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('ServiceDesk::lang.export_assets')}}</a></li>
    </ul>
</li>
@endif

@if($sdPolicy->contractsView())
<li class="treeview">
    <a href="#">
           <i class="fa fa-paperclip"></i> <span>{{Lang::get('ServiceDesk::lang.contract')}}</span> <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li><a href="{{url('service-desk/contracts')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i> {{Lang::get('ServiceDesk::lang.all_contracts')}}</a></li>
     @if($sdPolicy->contractCreate())
        <li class=""><a href="{{url('service-desk/contracts/create')}}" onclick="sidebaropen(this)"><i class="fa fa-circle-o"></i>{{Lang::get('ServiceDesk::lang.new_contract')}} </a></li>
     @endif
    </ul>
</li>
@endif

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
