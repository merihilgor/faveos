<?php
//$info = $problem->general();
//dd($info);
$root = "";
$symptom = "";
$impact = "";
$solution_title = "";
$solution = "";
$table = $problem->table();
$owner = "$table:$problem->id";
if ($problem->getGeneralByIdentifier('root-cause')) {
    $root = $problem->getGeneralByIdentifier('root-cause')->value;

    $delete_root_url = url('service-desk/general/' . $owner . '/root-cause/delete');
    $popid = "root-cause$problem->id";
    $title = "Delete Root Cause";
    $class = "btn btn-primary btn-xs";
    $delete_root_popup = \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::deletePopUp($popid, $delete_root_url, $title,$class, " ");
}
if ($problem->getGeneralByIdentifier('symptoms')) {
    $symptom = $problem->getGeneralByIdentifier('symptoms')->value;
    $delete_symptoms_url = url('service-desk/general/' . $owner . '/symptoms/delete');
    $popid = "symptoms$problem->id";
    $title = "Delete symptoms";
    $class = "btn btn-primary btn-xs";
    $delete_symptoms_popup = \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::deletePopUp($popid, $delete_symptoms_url, $title,$class, " ");
}
if ($problem->getGeneralByIdentifier('solution-title')) {
    $solution_title = $problem->getGeneralByIdentifier('solution-title')->value;
    //$delete_root_url = url('service-desk/general/'.$problem->id.'root-cause/delete');
}
if ($problem->getGeneralByIdentifier('solution')) {
    $solution = $problem->getGeneralByIdentifier('solution')->value;
    $delete_solution_url = url('service-desk/general/' . $owner . '/solution/delete');
    $popid = "solution$problem->id";
    $title = "Delete solution";
    $class = "btn btn-primary btn-xs";
    $delete_solution_popup = \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::deletePopUp($popid, $delete_solution_url, $title,$class, " ");
}
if ($problem->getGeneralByIdentifier('impact')) {
    $impact = $problem->getGeneralByIdentifier('impact')->value;
    $delete_impact_url = url('service-desk/general/' . $owner . '/impact/delete');
    $popid = "impact$problem->id";
    $title = "Delete impact";
    $class = "btn btn-primary btn-xs";
    $delete_impact_popup = \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::deletePopUp($popid, $delete_impact_url, $title, $class, " ");
}
?>
<style>
    .left-hand{
        float: left;
    }
</style>
<div class="row">

    <div class="col-md-12">
        <!-- The time line -->
        <ul class="timeline">
            @if($root!=="")
            <!--root-cause-->
            <li class="time-label">
                <p> &nbsp; </p>
                <span class="bg-green">
                 {{Lang::get('ServiceDesk::lang.root_cause')}}
                </span> 

            </li>

            <li>

                <i class="fa fa-ambulance bg-purple" title="Root Cause"></i>
                <div class="timeline-item">
                    <div class="timeline-header">
                        @if($problem->getGeneralByIdentifier('root-cause'))
               <a href="#" class="btn btn-default btn-xs" style="pointer-events: none;"> <i class="fa fa-clock-o" aria-hidden="true">&nbsp; {{ faveoDate($problem->getGeneralByIdentifier('root-cause')->created_at) }}</i></a>
                        @endif
                        <div class="row">
                            <div class="col-md-offset-10">
                                <a href="#delete" class= "btn btn-primary btn-xs" data-toggle="modal" title="Edit" data-target="#rootcause{{$problem->id}}"><i class="fa fa-pencil">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.edit')}}</a>&nbsp;
                                {!! $delete_root_popup !!}
                                
                            </div>
                        </div>
                    </div>

                    <div class="timeline-body" style="padding-left:30px;margin-bottom:-20px;word-wrap: break-word;">

                        {!! ucfirst($root) !!}
                    </div>
                    @if($problem->generalAttachments('root-cause')->count()>0)
                    <br><br>
                    <div class="timeline-footer" style="margin-bottom:-5px">
                        <div class="row">
                            @foreach($problem->generalAttachments('root-cause') as $attachment)
                            <?php
                            $deleteid = $attachment->id;
                            $deleteurl = url('service-desk/delete/' . $attachment->id . '/' . $attachment->owner . '/attachment');
                            ?>
                            @include('service::interface.agent.popup.delete')
                            <div class="col-md-3">
                                <ul class="list-unstyled clearfix mailbox-attachments left-hand">
                                    <li>
                                        <a href="{{url('service-desk/download/'.$attachment->id.'/'.$attachment->owner.'/attachment')}}">
                                            <span class="mailbox-attachment-icon" style="background-color:#fff;font-size:12px">{!!strtoupper($attachment->type)!!}</span>
                                            <div class="mailbox-attachment-info">
                                                <span>
                                                    <b style="word-wrap: break-word;">{!!$attachment->value!!}</b>
                                                    <br/>
                                                    <p>{{\App\Plugins\ServiceDesk\Controllers\Library\UtilityController::getAttachmentSize($attachment->size)}}</p>
                                                </span>

                                            </div>
                                        </a>
                                    </li>
                                    <a href="#change-detach" class="col-md-offset-12 fa fa-remove" data-toggle="modal" data-target="#delete{{$deleteid}}"></a>
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </li>
            <!--/root-cause-->
            @endif
            @if($impact!=="")
            <!--impact-->
            <li class="time-label">
                <p> &nbsp; </p>
                <span class="bg-green">
                    {{Lang::get('ServiceDesk::lang.impact')}}
                </span>     
            </li>
            <li>

                <i class="fa fa-anchor bg-purple" title="Impact"></i>
                <div class="timeline-item">
                    <div class="timeline-header">
                        @if($problem->getGeneralByIdentifier('impact'))
            <a href="#" class="btn btn-default btn-xs" style="pointer-events: none;"> <i class="fa fa-clock-o" aria-hidden="true">&nbsp;          {{faveoDate($problem->getGeneralByIdentifier('impact')->created_at) }}</i></a>
                        @endif
                        <div class="row">
                            <div class="col-md-offset-10">
                                <a href="#delete"  class="btn btn-primary btn-xs" data-toggle="modal" title="Edit" data-target="#impact{{$problem->id}}"><i class="fa fa-pencil">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.edit')}}</a>&nbsp;
                                {!! $delete_impact_popup !!}
                            </div>
                        </div>
                    </div>
                    <div class="timeline-body" style="padding-left:30px;margin-bottom:-20px;word-wrap: break-word;">

                        {!! ucfirst($impact) !!}
                    </div>
                
                @if($problem->generalAttachments('impact')->count()>0)
                <br><br>
                <div class="timeline-footer" style="margin-bottom:-5px">
                    <div class="row">
                        @foreach($problem->generalAttachments('impact') as $attachment)
                        <?php
                        $deleteid = $attachment->id;
                        $deleteurl = url('service-desk/delete/' . $attachment->id . '/' . $attachment->owner . '/attachment');
                        ?>
                        @include('service::interface.agent.popup.delete')
                        <div class="col-md-3">
                            <ul class="list-unstyled clearfix mailbox-attachments left-hand">
                                    <li>
                                        <a href="{{url('service-desk/download/'.$attachment->id.'/'.$attachment->owner.'/attachment')}}">
                                            <span class="mailbox-attachment-icon" style="background-color:#fff;font-size:12px">{!!strtoupper($attachment->type)!!}</span>
                                            <div class="mailbox-attachment-info">
                                                <span>
                                                    <b style="word-wrap: break-word;">{!!$attachment->value!!}</b>
                                                    <br/>
                                                    <p>{{\App\Plugins\ServiceDesk\Controllers\Library\UtilityController::getAttachmentSize($attachment->size)}}</p>
                                                </span>

                                            </div>
                                        </a>
                                    </li>
                                    <a href="#delete"  data-toggle="modal" class="col-md-offset-12 fa fa-remove" data-target="#delete{{$deleteid}}"></a>
                            </ul>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                </div>
            </li>
            <!--/impact-->
            @endif
            @if($symptom!=="")
            <!--symptoms-->
            <li class="time-label">
                <p> &nbsp; </p>
                <span class="bg-green">
                    {{Lang::get('ServiceDesk::lang.symptoms')}}
                </span>     
            </li>
            <li>

                <i class="fa fa-battery-0 bg-purple" title="Symptoms"></i>
                <div class="timeline-item">
                    <div class="timeline-header">
                        @if($problem->getGeneralByIdentifier('symptoms'))
                   <a href="#" class="btn btn-default btn-xs" style="pointer-events: none;"> <i class="fa fa-clock-o" aria-hidden="true">&nbsp;    {{faveoDate($problem->getGeneralByIdentifier('symptoms')->created_at)}}</i></a>

                        @endif
                        <div class="row">

                            <div class="col-md-offset-10">
                               <a href="#delete" data-toggle="modal" class="btn btn-primary btn-xs" title="Edit" data-target="#symptoms{{$problem->id}}"><i class="fa fa-pencil">&nbsp;</i>{{Lang::get('ServiceDesk::lang.edit')}}</a>&nbsp; {!! $delete_symptoms_popup !!}
                            </div>
                        </div>
                    </div>
                    <div class="timeline-body" style="padding-left:30px;margin-bottom:-20px;word-wrap:break-word;">

                        {!! ucfirst($symptom) !!}
                    </div>
                    @if($problem->generalAttachments('symptom')->count()>0)
                    <br><br>
                    <div class="timeline-footer" style="margin-bottom:-5px">
                        <div class="row">
                            @foreach($problem->generalAttachments('symptom') as $attachment)
                            <?php
                            $deleteid = $attachment->id;
                            $deleteurl = url('service-desk/delete/' . $attachment->id . '/' . $attachment->owner . '/attachment');
                            ?>
                            @include('service::interface.agent.popup.delete')
                            <div class="col-md-3">
                                <ul class="list-unstyled clearfix mailbox-attachments left-hand">
                                    <li>
                                        <a href="{{url('service-desk/download/'.$attachment->id.'/'.$attachment->owner.'/attachment')}}">
                                            <span class="mailbox-attachment-icon" style="background-color:#fff;font-size:12px">{!!strtoupper($attachment->type)!!}</span>
                                            <div class="mailbox-attachment-info">
                                                <span>
                                                    <b style="word-wrap: break-word;">{!!$attachment->value!!}</b>
                                                    <br/>
                                                    <p>{{\App\Plugins\ServiceDesk\Controllers\Library\UtilityController::getAttachmentSize($attachment->size)}}</p>
                                                </span>

                                            </div>
                                        </a>
                                    </li>
                                    <a href="#change-detach" class="col-md-offset-12 fa fa-remove"  data-toggle="modal" data-target="#delete{{$deleteid}}"></a>
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </li>
            <!--/sympton-->
            @endif
            @if($solution)
            <!--solution-->
            <li class="time-label">
                <p> &nbsp; </p>
                <span class="bg-green">
                   {{Lang::get('ServiceDesk::lang.solution')}}

                </span>     
            </li>
            <li>

                <i class="fa fa-thumbs-up bg-purple" title="Solution"></i>
                <div class="timeline-item">
                    <!--<span  class="time" style="color:#fff;"><i class="fa fa-clock-o"> </i> 25.05.2016 11:01:01</span>-->
                    <div class="timeline-header">
                        <h3 style="word-wrap: break-word;">{!! ucfirst($solution_title) !!}</h3>
                        @if($problem->getGeneralByIdentifier('solution'))
                      <a href="#" class="btn btn-default btn-xs" style="pointer-events: none;"> <i class="fa fa-clock-o" aria-hidden="true">&nbsp;  {{faveoDate($problem->getGeneralByIdentifier('solution')->created_at)}}</i></a>
                        @endif
                        <div class="row">
                            <div class="col-md-offset-10">
                                <a href="#delete" class="btn btn-primary btn-xs" data-toggle="modal" title="Edit" data-target="#solution{{$problem->id}}"><i class="fa fa-pencil">&nbsp;&nbsp;</i>{{Lang::get('ServiceDesk::lang.edit')}}</a>&nbsp;  {!! $delete_solution_popup !!}
                            </div>
                        </div>
                    </div>

                    <div class="timeline-body" style="padding-left:30px;margin-bottom:-20px;word-wrap:break-word;">
                        {!! ucfirst($solution) !!}


                    </div>
                    @if($problem->generalAttachments('solution')->count()>0)
                    <br><br>
                    <div class="timeline-footer" style="margin-bottom:-5px">
                        <div class="row">
                            @foreach($problem->generalAttachments('solution') as $attachment)
                            <?php
                            $deleteid = $attachment->id;
                            $deleteurl = url('service-desk/delete/' . $attachment->id . '/' . $attachment->owner . '/attachment');
                            ?>
                            @include('service::interface.agent.popup.delete')
                            <div class="col-md-3">
                                <ul class="list-unstyled clearfix mailbox-attachments left-hand">
                                    <li>
                                        <a href="{{url('service-desk/download/'.$attachment->id.'/'.$attachment->owner.'/attachment')}}">
                                            <span class="mailbox-attachment-icon" style="background-color:#fff;">{!!strtoupper($attachment->type)!!}</span>
                                            <div class="mailbox-attachment-info">
                                                <span>
                                                    <b style="word-wrap: break-word;">{!!$attachment->value!!}</b>
                                                    <br/>
                                                    <p>{{\App\Plugins\ServiceDesk\Controllers\Library\UtilityController::getAttachmentSize($attachment->size)}}</p>
                                                </span>

                                            </div>
                                        </a>
                                    </li>
                                    <a href="#change-detach" class="col-md-offset-12 fa fa-remove"  data-toggle="modal" data-target="#delete{{$deleteid}}"></a>
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </li>
            <!--/solution-->
            @endif
            @if($problem->change())
            <!--solution-->
            <li class="time-label">
                <p> &nbsp; </p>
                <span class="bg-green">
                    {{Lang::get('ServiceDesk::lang.change')}}
                </span>     
            </li>
            <li>

                <i class="fa fa-refresh bg-purple" title="change"></i>
                <div class="timeline-item">
                    <div class="timeline-body" style="padding-left:30px;margin-bottom:-20px">
                        <a href="{{url('service-desk/changes/'.$problem->change()->id.'/show')}}" title="{{$problem->change()->subject}}"><b>#CHN-{{ $problem->change()->id }}</b> &nbsp;&nbsp;{!! ucfirst(str_limit($problem->change()->subject,20)) !!}</a>
                    </div>
                    <br><br>
                    <div class="timeline-footer" style="margin-bottom:-5px">
                        <ul class="mailbox-attachments clearfix">
                        </ul>
                    </div>
                </div>
            </li>
            <!--/solution-->
            @endif
        </ul>
    </div>
</div>
<script type="text/javascript">
     if('{{Lang::getLocale()}}'=='ar'){
      setTimeout(function(){
         $('.left-hand').css('float','right');
    },100)
  }
</script>