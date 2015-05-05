@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-6 medium-offset-3 columns">
			<div class="panel extra">
				<h2 class="light-header">Notifications</h2>
				<hr>
				@foreach(Auth::user()->rNotifications()->orderBy('id', 'DESC')->get() as $n)
					<div class="notification-entry">
						<h5 class="{{ ($n->read == false ? 'bold' : '') }}">
							<span>{{ $n->title() }}</span>
						</h5>
                        @if ($n->type == \App\Enums\NotificationTypes::COMMENT_REPLY)
                            <a class="text-primary" href="{{ $n->comment()->post()->getPermalink() }}">{{ $n->comment()->post()->title }}</a>
                        @endif
						<p>
                            @if ($n->type == \App\Enums\NotificationTypes::REP)
							    <span class="bold">{{ sprintf("%+d",$n->repEvent()->amount) }} rep:</span> {{ $n->repEvent()->event }}
                            @elseif ($n->type == \App\Enums\NotificationTypes::COMMENT_REPLY)
                                {!! $n->comment()->self_text !!}
                            @endif
						</p>
						<ul class="inline-list">
							<li>
								<a id="markNotificationAsReadBtn" data-nid="{{ $n->id }}">mark as {{ ($n->read == true ? 'un' : '') }}read</a>
							</li>
                            @if ($n->type == \App\Enums\NotificationTypes::COMMENT_REPLY)
                            <li>
                                <a href="{{ $n->comment()->getPermalink() }}?context=3">context</a>
                            </li>
                            @endif
						</ul>
					</div>
				@endforeach
			</div>
		</div>
	</div>
</section>
<input type="hidden" id="csrftoken" value="{{ csrf_token() }}">
@stop