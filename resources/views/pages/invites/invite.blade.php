@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-12 columns">
			<div class="panel extra">
				<div class="row">
					<div class="medium-10 medium-offset-1 columns">
						<h2 class="light-header">
							Submitting an invite
						</h2>
						<br>
						<p>
							General rules when submitting an invite:
						</p>
						<ul>
							<li>Rule #1: </li>
							<li>Rule #2: </li>
							<li>Rule #3: </li>
						</ul>
						<br>
						<hr>
						<br>
						@if(count($errors->all()) > 0)
							<ul class="no-bullet text-alert smaller-fs">
								@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
								@endforeach
							</ul>
						@endif
						@if (Session::has('notice'))
							@if (Session::get('notice')[0] == 'error')
							<ul class="no-bullet text-alert smaller-fs">
							@else
							<ul class="no-bullet smaller-fs">
							@endif
								<li>{{ Session::get('notice')[1] }}</li>
							</ul>
						@endif
						<div class="invite-form">
							<input type="hidden" value="{{ csrf_token() }}" id="csrfToken">
							<input type="hidden" value="0" id="selectedGameId">
							<h3>
								I'm looking for <input class="big-inline-input" type="text" placeholder="0" id="maxPlayers">
								players to play the game <input class="big-inline-input" type="text" placeholder="Type to search game" id="gameSearchInput">
								<div class="game-search-results-container">
	                            	<div class="game-search-results" id="gameSearchResults"></div>
	                            </div>
								on the <select id="console" disabled="true" class="disabled">
									<option value="0">select a game first</option>
								</select>.
							</h3>
							<br><br><br>
							<div class="more-details disabled" id="moreDetails">
								<h2>
									<input type="text" class="disabled" id="inviteTitle" placeholder="Title of this invite">
								</h2>
								<textarea id="inviteText" class="disabled" placeholder="More info about this invite"></textarea>
							</div>
                            <br><br><br>
                            <label><input type="checkbox"> Only verified users can respond on this invite.</label>
						    {!! Recaptcha::render() !!}
						    <br>
						    <button type="submit" class="btn big primary" id="inviteSubmitter">Submit invite</button>
						    &nbsp;
						    <img id="progresser" src="{!! url('/img/loaders/dots.svg') !!}" width="40px">
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@stop