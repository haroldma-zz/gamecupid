@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-8 columns">
			<div class="panel">
				<div class="feed">
					<div class="sort-by">
						<a href="{!! url('/') !!}">Featured</a>
						<a href="{!! url('/?sort=hot') !!}" class="{{ (Request::get('sort') == 'hot' || Request::get('sort') == '' ? 'active' : '') }}">Hot</a>
						<a href="{!! url('/?sort=new') !!}" class="{{ (Request::get('sort') == 'new' ? 'active' : '') }}">New</a>
						<a href="{!! url('/?sort=controversial') !!}" class="{{ (Request::get('sort') == 'controversial' ? 'active' : '') }}">Controversial</a>
						<a href="{!! url('/?sort=top') !!}" class="{{ (Request::get('sort') == 'top' ? 'active' : '') }}">Top</a>
					</div>
					<hr>
					@if (count($invites) > 0)
						@foreach($invites as $invite)
						<article class="invite">
							<header>
								<div class="img"></div>
								<div class="user-meta">
									<h6>
										<a href="{!! url('/') !!}">{{ $invite->user->username }}</a>
									</h6>
									<p>
										{{ Timeago::convert($invite->created_at) }}
									</p>
								</div>
							</header>
							<section>
								<h3>
									<div class="voters">
										<div class="arrows">
											<div id="upvoter" data-invite-id="{{ $invite->id }}">
												<i class="ion-arrow-up-a {{ ($invite->isUpvoted() ? 'activated' : '') }}" id="upvoter-{{ $invite->id }}"></i>
											</div>
											<div id="downvoter" data-invite-id="{{ $invite->id }}">
												<i class="ion-arrow-down-a {{ ($invite->isDownvoted() ? 'activated' : '') }}" id="downvoter-{{ $invite->id }}"></i>
											</div>
										</div>
										<div class="count" id="voteCount-{{ $invite->id }}">
											{{ $invite->upvoteCount() - $invite->downvoteCount() }}
										</div>
									</div>
									{{ $invite->title }}
								</h3>
							</section>
							<footer>
								<a>{{ $invite->player_count }} player{{ ($invite->player_count > 1 ? 's' : '') }}</a>
								<a>&middot;</a>
								<a href="{!! url('/invite/' . $invite->hashid() . '/' . $invite->slug) !!}"><b>Let's play!</b></a>
								<a>&middot;</a>
								<a href="{!! url('/invite/' . $invite->hashid() . '/' . $invite->slug) !!}">{{ $invite->comments->count() }} comments</a>
							</footer>
							<hr>
						</article>
						@endforeach
					@else
						<h5>
							There are no invites yet...
						</h5>
					@endif
				</div>
			</div>
		</div>
		<div class="medium-3 medium-offset-1 columns">
			<div class="panel">
				<h5 class="super-header">sidebar</h5>
			</div>
		</div>
	</div>
</section>
<input type="hidden" id="csrfToken" value="{{ csrf_token() }}">
@stop