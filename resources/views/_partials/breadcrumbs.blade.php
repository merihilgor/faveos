@if (count($breadcrumbs))

    <ol class="breadcrumb">
    	<li class="text"> {!! Lang::get('lang.you_are_here') !!} :</li>

        @foreach ($breadcrumbs as $breadcrumb)

            @if ($breadcrumb->url && !$loop->last)
                <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{!! $breadcrumb->title !!}</a></li>
            @else
                <li class="breadcrumb-item active">{!! $breadcrumb->title !!}</li>
            @endif

        @endforeach
    </ol>

@endif