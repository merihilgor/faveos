<li class="dropdown notifications-menu">

@if(\Event::dispatch('helpdesk.apply.whitelabel'))
 
@else
@if(Auth::user()->role == 'admin')
 <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Application Updates" style="padding:15px ">
        <i class="fa fa-refresh"></i>
        <span class="label label-danger {!! $notification->count() ? "auto-update-blink" : "" !!}" id="count">{!! $notification->count() !!}</span>
    </a> 
@endif
@endif

    <ul class="dropdown-menu" style="width:500px">
        
        <li class="header">You have {!! $notification->count() !!} update(s).</li>

        <ul class="menu list-unstyled">
            @if($notification->count()>0)
                @foreach($notification as $notify)
                    @if($notify->value)
                        <br><li>&nbsp;&nbsp;&nbsp;{!! $notify->value !!}</li><br>
                        <li class="clearfix"></li>
                    @endif
            @endforeach
            @endif

        </ul>
        <!--<li class="footer no-border"><div class="col-md-5"></div><div class="col-md-2">
                <img src="{{asset("lb-faveo/media/images/gifloader.gif")}}" style="display: none;" id="notification-loader">
            </div><div class="col-md-5"></div></li>
        <li class="footer"><a href="{{ url('notifications-list')}}">View all</a>
        </li>-->

    </ul>
</li>

<style>

    .auto-update-blink {
        display:inline-block;
        animation: blink 2s ease-in infinite;
    }

    @keyframes blink {
        from, to { opacity: 1 }
        50% { opacity: 0 }
    }
</style>