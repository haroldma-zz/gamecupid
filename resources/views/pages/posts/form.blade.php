@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-12 columns">
			<div class="panel extra">
				<div class="row">
					<div class="medium-10 medium-offset-1 columns">
						<h2 class="light-header">
							Submitting a post
						</h2>
						<br>
						<p>
							General rules when submitting an post:
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
						<div class="post-form">
							<input type="hidden" value="{{ csrf_token() }}" id="csrfToken">
							<input type="hidden" value="0" id="selectedGameId">
							<h3>
								I'm looking for <input class="big-inline-input" type="text" placeholder="0" id="maxPlayers">
								players to play the game <input class="big-inline-input" type="text" placeholder="Type to search game" id="gameSearchInput">
								<div class="game-search-results-container">
	                            	<div class="game-search-results" id="gameSearchResults"></div>
	                            </div>
								on the  <select id="console" disabled="true" class="disabled" name="console_id">
											<option value="0">select a game first</option>
										</select>.
							</h3>
							<br><br><br>
							<div class="more-details disabled" id="moreDetails">
								<h2>
									<input type="text" class="disabled" id="postTitle" placeholder="Title of this post">
								</h2>
								<textarea id="postText" class="disabled" placeholder="More info about this post"></textarea>
							</div>
                            <br><br><br>
                            <h3>More settings</h3>
                            <label>
                            	Choose a category that fits this submission:
                            </label>
							<div>
	                            <label class="radio-label"><input type="radio" name="category" value="anytime" checked> Anytime</label>
								<label class="radio-label"><input type="radio" name="category" value="asap"> As Soon As Possible</label>
								<label class="radio-label"><input type="radio" name="category" value="planned"> Planned</label>
								<div class="clearfix"></div>
							</div>
							<div id="plannedSection" class="hide">
								<br>
								<h5>
									Specify a period in which you wish to play:
								</h5>
								<div class="row">
									<div class="medium-2 columns">
										<label>
											Starting
										</label>
									</div>
									<div class="medium-5 columns">
										<label>Date</label>
										<input type="text" id="startDate" placeholder="MM-DD-YYYY" class="form-class dateinput">
									</div>
									<div class="medium-5 columns">
										<label>Time</label>
										<input type="text" id="startTime" placeholder="22:15" class="form-class timeinput">
									</div>
								</div>
								<div class="row">
									<div class="medium-2 columns">
										<label>
											Ending
										</label>
									</div>
									<div class="medium-5 columns">
										<label>Date</label>
										<input type="text" id="endDate" placeholder="MM-DD/-YYY" class="form-class dateinput">
									</div>
									<div class="medium-5 columns">
										<label>Time</label>
										<input type="text" id="endTime" placeholder="22:15" class="form-class timeinput">
									</div>
								</div>
							</div>
							<br><br>
                            <label><input type="checkbox" id="verifiedInput"> I only want to play with verified users.</label>
						    <button type="submit" class="btn big primary" id="postSubmitter">Submit post</button>
						    &nbsp;
						    <img id="progresser" src="{!! url('/img/loaders/dots.svg') !!}" width="40px">
						    <br><br>
						    <ul class="text-alert" id="submitError"></ul>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@stop