@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-8 medium-offset-2 columns">
			<div class="panel extra">
				<h2 class="light-header">Notifications</h2>
				<br>
				@foreach($notifications as $n)
					<div class="notification-entry" id="notificationContainer" data-nid="{{ $n->id }}">
						<h5 class="{{ ($n->read == false ? 'bold' : '') }}">
							<span>{{ $n->title() }}</span>
						</h5>
                        @if ($n->type == \App\Enums\NotificationTypes::COMMENT_REPLY || $n->type == \App\Enums\NotificationTypes::POST_COMMENT)
                            <a class="text-primary" href="{{ $n->comment()->post()->getPermalink() }}">{{ $n->comment()->post()->title }}</a>
                        @endif
						<p>
                            @if ($n->type == \App\Enums\NotificationTypes::REP)
							    <span class="bold">{{ sprintf("%+d",$n->repEvent()->amount) }} rep:</span> {{ $n->repEvent()->event }}
                            @elseif ($n->type == \App\Enums\NotificationTypes::COMMENT_REPLY || $n->type == \App\Enums\NotificationTypes::POST_COMMENT)
                                {!! $n->comment()->self_text !!}
                            @elseif ($n->type == \App\Enums\NotificationTypes::INVITE_REQUEST)
                            	from <a href="{{ url('/gamer/' . $n->from->username) }}">{{ $n->from->username }}</a> on your post <a href="{{ $n->post()->getPermalink() }}">{{ $n->post()->title }}</a>
                            @elseif ($n->type == \App\Enums\NotificationTypes::DECLINED_INVITE)
                            	for <a href="{{ $n->post()->getPermalink() }}">{{ $n->post()->title }}</a>
                            @endif
						</p>
						<ul class="inline-list">
                            @if ($n->type == \App\Enums\NotificationTypes::COMMENT_REPLY || $n->type == \App\Enums\NotificationTypes::POST_COMMENT)
                            <li>
                                <a href="{{ $n->comment()->getPermalink() }}?context=3">context</a>
                            </li>
                            @elseif ($n->type == \App\Enums\NotificationTypes::INVITE_REQUEST)
								@if ($n->request->state == \App\Enums\RequestStates::PENDING)
	                            <li>
	                            	<a href="{{ url('/' . Auth::user()->username . '/session/' . hashId($n->request->id)) . '/accept' }}">Accept</a>
	                            </li>
	                            <li>
	                            	<a href="{{ url('/' . Auth::user()->username . '/session/' . hashId($n->request->id)) . '/decline' }}">Declined</a>
	                            </li>
	                            @elseif ($n->request->state == \App\Enums\RequestStates::DECLINED)
								<li>
									this invite request was declined
								</li>
	                            @elseif ($n->request->state == \App\Enums\RequestStates::ACCEPTED)
								<li>
									<a href="{{ url('/' . Auth::user()->username . '/session/' . hashId($n->gameSession->id)) }}">Go to session</a>
								</li>
	                            @endif
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