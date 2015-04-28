@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-9 columns">
			<div class="panel">
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
						<div class="panel markdown-text">
							{!! $invite->self_text !!}
						</div>
					</section>
					<footer>
						<a>{{ $invite->player_count }} player{{ ($invite->player_count > 1 ? 's' : '') }}</a>
						<a>&middot;</a>
						<a href="{!! url('/invite/' . $invite->hashid() . '/' . $invite->slug) !!}"><b>Let's play!</b></a>
						<a>&middot;</a>
						<a href="{!! url('/invite/' . $invite->hashid() . '/' . $invite->slug) !!}">0 comments</a>
					</footer>
					<hr>
				</article>
				<div class="comments">
					<div class="comment-box">
						{!! Form::open(['url' => '/invite/' . Hashids::encode($invite->id) . '/' . $invite->slug]) !!}
						{!! Form::hidden('parent_id', 0) !!}
						{!! Form::hidden('invite_id', $invite->id) !!}
						{!! Form::label('self_text', 'You can use Markdown to write comments.') !!}
						{!! Form::textarea('self_text', '', ['class' => 'form-control', 'placeholder' => 'Write a comment']) !!}
						<button type="submit" class="btn primary medium">Comment</button>
						{!! Form::close() !!}
						@if(Session::has('notice'))
							@if (Session::get('notice')[0] == 'error')
							<p class="text-alert smaller-fs">{{ Session::get('notice')[1] }}</p>
							@else
							<p class="text-success smaller-fs">{{ Session::get('notice')[1] }}</p>
							@endif
						@endif
					</div>
					<br>
					<h6 class="comments-header">
						<div class="left">
							Comments ({{ $invite->comments->count() }})
						</div>
						<div class="right">
							<small>
								<a href="?sort=best" class="{{ (Request::get('sort') == 'best' || Request::get('sort') == '' ? 'active' : '') }}">best</a>
								<a href="?sort=hot" class="{{ (Request::get('sort') == 'hot' ? 'active' : '') }}">hot</a>
								<a href="?sort=new" class="{{ (Request::get('sort') == 'new' ? 'active' : '') }}">new</a>
								<a href="?sort=top" class="{{ (Request::get('sort') == 'top' ? 'active' : '') }}">top</a>
								<a href="?sort=controversial" class="{{ (Request::get('sort') == 'controversial' ? 'active' : '') }}">controversial</a>
							</small>
						</div>
						<div class="clearfix"></div>
					</h6>
					{!! $invite->renderComments("best") !!}
				</div>
			</div>
		</div>
		<div class="medium-3 columns">
			<div class="panel">
				<h6>
					<b>{{ $invite->game->title }}</b>
				</h6>
				<p>
					{{ $invite->game->description }}
				</p>
			</div>
		</div>
	</div>
</section>
<input type="hidden" id="csrfToken" value="{{ csrf_token() }}">
@stop