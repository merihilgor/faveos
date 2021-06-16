<?php
$lang = \Lang::getLocale();
?>

<div class="{!! $lang == 'ar'? "ar": "nonar" !!}}">
	<h2 style="text-align: center"><u>{!! "#".$tickets->ticket_number."" !!}</u></h2>
	<br>
	<table class="result_table">
		<tr class="info-row">
			<td><b>{{Lang::get('lang.created_at')}}:</b></td><td>&nbsp;&nbsp;{!! faveoDate($ticket->created_at)  !!}</td>
		</tr>
		<tr class="info-row">
			<td><b>{{Lang::get('lang.subject')}}:</b></td><td>&nbsp;&nbsp;{!!$tickets->title  !!}</td>
		</tr>
		<tr class="info-row">
			<td><b>{{Lang::get('lang.department')}}:</b></td><td>&nbsp;&nbsp;{!! $tickets->department  !!}</td>
		</tr>
		<tr class="info-row">
			<td><b>{{Lang::get('lang.help_topic')}}:</b></td><td>&nbsp;&nbsp;{!! $tickets->helptopic  !!}</td>
		</tr>
	</table>
	<br>
	<h3>{!! trans("lang.ticket-conversation-title") !!}</h3>
	<ul>
		@forelse($ticket->thread as $thread)

			<?php
				$name = $thread->user ? $thread->user->meta_name : "System";
			?>

			<li>
				<div style="margin-top:-2px">{!! trans("lang.posted_by_on", ["user"=> "<strong>".htmlentities($name)."</strong>", "time"=> faveoDate($thread->created_at)]) !!}</div>
				<div style="background-color: rgba(0,0,0,0.04); padding: 10px">{!! $thread->pdf_able_body !!}</div>
				<br>
			</li>

		@empty
		@endforelse
	</ul>
</div>


<style type="text/css">
	.ar{
		direction: rtl;
		text-align: right;
		font-family: 'dejavu sans', sans-serif;
	}

	.nonar{
		font-family: 'dejavu sans', sans-serif;
		font-size: small;
	}

	.info-row{
		border-top: 1px solid #f4f4f4;
		padding: 10px;
		box-sizing: border-box;
	}

	img {
		max-width: 500px !important;
	}
</style>

