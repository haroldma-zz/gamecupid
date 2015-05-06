@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-9 columns">
			<div class="panel">
				<div class="feed" id="feedContainer" data-page="1" data-category="anytime">
					<div class="sort-by">
						<a href="{!! url('/?category=featured') !!}">Featured</a>
						<a href="{!! url('/?category=today') !!}" class="{{ (Request::get('category') == 'today' || Request::get('category') == '' ? 'active' : '') }}">Today</a>
						<a href="{!! url('/?category=asap') !!}" class="{{ (Request::get('category') == 'asap' ? 'active' : '') }}">ASAP</a>
						<a href="{!! url('/?category=upcoming') !!}" class="{{ (Request::get('category') == 'upcoming' ? 'active' : '') }}">Upcoming</a>
						<a href="{!! url('/?category=anytime') !!}" class="{{ (Request::get('category') == 'anytime' ? 'active' : '') }}">Anytime</a>
					</div>
					<hr>
					@if (count($posts) > 0)
						@foreach($posts as $post)
						<article data-id="{{  hashId($post->id) }}" class="post">
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
							<hr>
						</article>
						@endforeach
					@else
						<h5>
							There are no posts yet...
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
		<div class="medium-3 columns">
			<div class="panel">
				<h5 class="super-header" style="letter-spacing: 3px;">Hot games</h5>
				<ol class="text-justify">
					<li>
						<a href="">Grand Theft Auto V</a>
					</li>
					<li>
						<a href="">FIFA15</a>
					</li>
					<li>
						<a href="">Call of Duty: Black Ops II</a>
					</li>
					<li>
						<a href="">Bloodborne</a>
					</li>
					<li>
						<a href="">Battlefield 4</a>
					</li>
					<li>
						<a href="">The Last of Us</a>
					</li>
				</ol>
			</div>
			<div class="panel">
				<h5 class="super-header" style="letter-spacing: 3px;">Top gamers</h5>
				<ol class="text-justify">
					@foreach($topPlayers as $gamer)
					<li><a href="{!! url('/gamer/' . $gamer->username) !!}">{{ $gamer->username }}</a></li>
					@endforeach
					{!! var_dump($topPlayers) !!}
				</ol>
			</div>
		</div>
	</div>
</section>
<input type="hidden" id="csrfToken" value="{{ csrf_token() }}">
<input id="feedCategory" type="hidden" value="{{ Request::get('category', 'anytime') }}">
<input id="limit" type="hidden" value="{{ Request::get('limit', 10) }}">
@stop

@section('scripts')
@include('js.feed-loader')
@stop