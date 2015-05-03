@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-9 columns">
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
							<section>
								<h4 class="meta-details">
									<a href="{!! $invite->getPermalink() !!}">{{ $invite->title }}</a>
									<br>
									<small>
										<b>{{ $invite->game()->title }}</b>
										<br>
										<time data-livestamp="{{ $invite->created_at->getTimestamp() }}"></time>
										by
										<b>
											<a href="{!! url('/gamer/' . $invite->user->username) !!}">{{ $invite->user->username }}</a>
										</b>
										&nbsp;&middot;&nbsp;
										<b>{{ $invite->accepts->where('state', 2)->count() }}</b>{{ '/'.$invite->max_players }} player{{ ($invite->max_players > 1 ? 's' : '') }}
									</small>
								</h4>
								<div class="tagLabels">
	                                <span class="tagLabel" title="{{ $invite->console()->name  }}">{{ strtoupper($invite->console()->name)  }}</span>
	                                @if ($invite->verified_only)
	                                    <span class="tagLabel verified" title="Verified Only">VERIFIED ONLY</span>
	                                @endif
								</div>
							</section>
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
		<div class="medium-3 columns">
			<div class="panel">
				<h5 class="super-header" style="letter-spacing: 5px;">Top crews</h5>
				<ol class="no-bullet text-justify">
					<li>
						<b>noodlesFTW</b>
					</li>
					<li>
						<b>xBoners</b>
					</li>
					<li>
						<b>AyyLMAO</b>
					</li>
					<li>
						<b>LameAssCrewname</b>
					</li>
					<li>
						<b>OfficialYoutubers</b>
					</li>
					<li>
						<b>GanjaArmy</b>
					</li>
					<li>
						<b>BeerAndWeed</b>
					</li>
					<li>
						<b>Amsterdamned</b>
					</li>
				</ol>
			</div>
			<div class="panel">
				<h5 class="super-header" style="letter-spacing: 3px;">1-on-1 quick invites</h5>
				<ul class="no-bullet text-justify">
					<li>
						<a href="">FIFA15 - Real Madrid vs ?</a>
					</li>
					<li>
						<a href="">COD - Quickscope match</a>
					</li>
					<li>
						<a href="">GTAV - Online missions</a>
					</li>
					<li>
						<a href="">Age of Empires II - Online match</a>
					</li>
					<li>
						<a href="">The Last of Us - Co-op</a>
					</li>
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