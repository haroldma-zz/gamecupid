@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-8 columns">
			<div class="panel">
				<div class="feed" id="feedContainer" data-page="1" data-sort="hot">
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
						<article data-id="{{  hashId($invite->id) }}" class="invite">
							<header>
								<div class="img"></div>
								<div class="user-meta">
									<h6>
										<a href="{!! url('/') !!}">{{ $invite->user->username }}</a>
									</h6>
									<p>
										<time data-livestamp="{{ $invite->created_at->getTimestamp() }}"></time>
									</p>
								</div>
							</header>
							<section>
								<h3>
									<div class="voters">
										<div class="arrows">
											<div id="upvoter" data-invite-id="{{ hashId($invite->id) }}">
												<i class="ion-arrow-up-a {{ ($invite->isUpvoted() ? 'activated' : '') }}" id="upvoter-{{ hashId($invite->id) }}"></i>
											</div>
											<div id="downvoter" data-invite-id="{{ hashId($invite->id) }}">
												<i class="ion-arrow-down-a {{ ($invite->isDownvoted() ? 'activated' : '') }}" id="downvoter-{{ hashId($invite->id) }}"></i>
											</div>
										</div>
										<div class="count" id="voteCount-{{ hashId($invite->id) }}">
											{{ $invite->totalVotes() }}
										</div>
									</div>
									{{ $invite->title }}
								</h3>
							</section>
							<footer>
							<div class="tagLabels">
                                <span class="tagLabel" title="{{ $invite->console()->name  }}">{{ strtoupper($invite->console()->name)  }}</span>
                                @if ($invite->verified_only)
                                    <span class="tagLabel verified" title="Verified Only">VERIFIED ONLY</span>
                                @endif
							</div>
                                <a><span class="bold">{{ $invite->accepts->where('state', 2)->count() }}</span>{{ '/'.$invite->max_players }} player{{ ($invite->max_players > 1 ? 's' : '') }}</a>
								<a>&middot;</a>
								<a href="{!! $invite->getPermalink() !!}"><b>Let's play!</b></a>
								<a>&middot;</a>
								<a href="{!! $invite->getPermalink() !!}">{{ $invite->commentCount() }} comment{{ $invite->commentCount() == 1 ? "" : "s" }}</a>
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
				<div id="feedLoader" class="hide loader">
				  <div class="diamond"></div>
				  <div class="diamond"></div>
				  <div class="diamond"></div>
				</div>
			</div>
		</div>
		<div class="medium-3 medium-offset-1 columns">
			<div class="panel">
				<h5 class="super-header">TODO list</h5>
				<ul class="no-bullet text-justify">
					<li><b>To do</b></li>
					<li>Figure out what this area is for</li>
					<li>Accepting invites</li>
					<li>Private messaging</li>
					<li>Crew creation page</li>
					<li>User profile page</li>
					<li>User settings page</li>
					<li>Implement Gravatar for profile pics</li>
					<li>API design</li>
					<li>Show the game title on invites instead of only the title of the invite (maybe with a small label or something) (make it like flair on reddit maybe?)</li>
					<li>Show the level/rep label of users next to their name</li>
					<hr>
					<li><b>Ideas under discussion</b></li>
					<li><i>Make betting (for money, or something else) possible.</i></li>
					<li><i>Make it possible to submit tournaments and challenges.</i></li>
					<li><i></i></li>
				</ul>
			</div>
		</div>
	</div>
</section>
<input type="hidden" id="csrfToken" value="{{ csrf_token() }}">
<input id="sortType" type="hidden" value="{{ Request::get('sort', 'hot') }}">
<input id="limit" type="hidden" value="{{ Request::get('limit', 10) }}">
@stop

@section('scripts')
@include('js.feed-loader')
@stop