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
								<time datetime="{{ $invite->created_at }}"></time>
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
						<div class="panel markdown-text">
							{!! $invite->self_text !!}
						</div>
					</section>
					<footer>
						<a>{{ $invite->player_count }} player{{ ($invite->player_count > 1 ? 's' : '') }}</a>
						<a>&middot;</a>
						<a href="{!! $invite->getPermalink() !!}"><b>Let's play!</b></a>
						<a>&middot;</a>
						<a href="{!! $invite->getPermalink() !!}">{{ $invite->commentCount() }} comment{{ $invite->commentCount() == 1 ? '' : 's' }}</a>
					</footer>
					<hr>
				</article>
				<div class="comments">
					<div class="comment-box">
						{!! Form::open(['url' => $invite->getPermalink()]) !!}
						{!! Form::hidden('parent_id', 0) !!}
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
							Comments ({{ $invite->commentCount() }})
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
					@if (isset($comment))
                        {!! $comment->renderComments(Request::get("sort"), $context) !!}
					@else
						{!! $invite->renderComments(Request::get("sort")) !!}
					@endif
				</div>
			</div>
		</div>
		<div class="medium-3 columns">
			<div class="panel">
				<h6>
					<b>{{ $invite->game()->title }}</b>
				</h6>
				<p>
					{{ $invite->game()->description }}
				</p>
			</div>
		</div>
	</div>
</section>
<input type="hidden" id="csrfToken" value="{{ csrf_token() }}">
@stop

@section('scripts')
	{!! HTML::script('js/comment-collapser.js') !!}
@stop