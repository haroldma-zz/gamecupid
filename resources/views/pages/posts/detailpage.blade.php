@extends('base.base')

@section('page')
<section class="page">
	<div class="row medium-collapse detailpage">
		<div class="medium-10 medium-offset-1 columns">
			<div class="panel">
				<article class="post">
					<div class="row medium-collapse">
						<div class="medium-5 columns">
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
											<a href="{!! url('/gamer/' . $post->user->username) !!}">{{ $post->user->username }} <span class="header-rep-count"><b>{{ $post->user->level() }}</b>:{{ $post->user->rep() }}</span></a>
										</b>
										&nbsp;&middot;&nbsp;
										<b>{{ $post->requests->where('state', 2)->count() }}</b>{{ '/'.$post->max_players }} player{{ ($post->max_players > 1 ? 's' : '') }}
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
							@if ($post->user->id === Auth::id())
							<p>
								You submitted this post on <b>{{ date('M j, Y', strtotime($post->created_at)) }}</b>.
							</p>
							@else
							<button id="requestInviteBtn" class="btn success" {{ (Auth::user()->requests->where('post_id', $post->id)->count() !== 0 ? 'disabled' : '') }}>
								@if (Auth::user()->requests->where('post_id', $post->id)->count() !== 0)
								You have requested an invite.
								@else
								Request an invite
								@endif
							</button><img id="requestLoader" src="{!! url('/img/loaders/dots.svg') !!}" width="40px">
							<br>
							@endif
							<br><br>
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
										<textarea class="form-control" placeholder="Write a comment" data-parenthash="{!! hashId(0) !!}" data-url="{!! url($post->getPermalink()) !!}" data-hierarchy="child" data-level="no-parent"></textarea>
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
						<div class="medium-7 columns">
							<h5>
								Description
							</h5>
							<div class="panel markdown-text">
								{!! $post->self_text !!}
							</div>
						</div>
					</div>
				</article>
			</div>
			<div class="panel">
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
	@include('js.requester')
	@else
	<script>
		$('[id="commentSubmitter"], #requestInviteBtn').click(function()
		{
			window.location.href = "/login";
		});
	</script>
	@endif
	{!! HTML::script('js/comment-collapser.js') !!}
@stop