@extends('base.base')

@section('page')
<section class="page">
	<div class="row medium-collapse detailpage">
		<div class="medium-5 columns fixed-column">
			<div class="panel">
				<article class="post">
					<section>
						<h4 class="meta-details">
							<a href="{!! $post->getPermalink() !!}">{{ $post->title }}</a>
							<br>
							<small>
								<b>{{ $post->game()->title }}</b>
								<br>
								<time data-livestamp="{{ $post->created_at->getTimestamp() }}"></time>
								by
								<b>
									<a href="{!! url('/gamer/' . $post->user->username) !!}">{{ $post->user->username }}</a>
								</b>
								&nbsp;&middot;&nbsp;
								<b>{{ $post->accepts->where('state', 2)->count() }}</b>{{ '/'.$post->max_players }} player{{ ($post->max_players > 1 ? 's' : '') }}
							</small>
						</h4>
						<div class="tagLabels">
                            <span class="tagLabel" title="{{ $post->console()->name  }}">{{ strtoupper($post->console()->name)  }}</span>
                            @if ($post->verified_only)
                                <span class="tagLabel verified" title="Verified Only">VERIFIED ONLY</span>
                            @endif
						</div>
					</section>
					<br><br>
					<a href="" class="btn success">
						Request an invite
					</a>
					<hr>
				</article>
				<div class="comments">
					<div class="comment-box">
						<h6>
							Leave a comment
						</h6>
						<p>
							<i>You can use Markdown to write comments.</i>
						</p>
						<div class="relative">
							<section id="emojis" class="emoji-intellisense">
								<div class="emoji"></div>
							</section>
							<textarea class="form-control" placeholder="Write a comment" data-parenthash="{!! hashId(0) !!}" data-url="{!! url($post->getPermalink()) !!}" data-hierarchy="parent" data-level="no-parent"></textarea>
						</div>
						<button type="submit" class="btn primary medium" id="commentSubmitter">Comment</button>
						<img id="progresser" src="{!! url('/img/loaders/dots.svg') !!}" width="40px">
						<div></div>
						@if(Session::has('notice'))
							@if (Session::get('notice')[0] == 'error')
							<p class="text-alert smaller-fs">{{ Session::get('notice')[1] }}</p>
							@else
							<p class="text-success smaller-fs">{{ Session::get('notice')[1] }}</p>
							@endif
						@endif
					</div>
				</div>
			</div>
		</div>
		<div class="medium-7 columns">
			<div class="panel">
				<h5>
					<b>{{ $post->game()->title }}</b>
				</h5>
				<p>
					{{ $post->game()->description }}
				</p>
				<br>
				<div class="markdown-text">
					{!! $post->self_text !!}
				</div>
				<br><br><br>
				<div class="comments">
					<h6 class="comments-header">
						<div class="left">
							Comments (<span id="postCommentCount">{{ $post->commentCount() }}</span>)
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
						<div id="commentsList">
							{!! $comment->renderComments(Request::get("sort"), $context) !!}
						</div>
					@else
						<div id="commentsList">
							{!! $post->renderComments(Request::get("sort")) !!}
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</section>
<input type="hidden" id="csrfToken" value="{{ csrf_token() }}">
@stop

@section('scripts')
	@if(Auth::check())
	@include('js.commenter')
	@endif
	{!! HTML::script('js/comment-collapser.js') !!}
@stop