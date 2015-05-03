@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-12 columns">
			<div class="panel extra">
				<div class="row">
					<div class="medium-10 medium-offset-1 columns">
						<h2 class="light-header">
							Crew creator
						</h2>
						<br>
						<p>
							A crew gets it's own page which the mods can customize to their liking. Within a crew, members can submit invites, text posts, images, videos and <i>events</i>. You can make your crew public, semi-private or private. Members get a crew tag behind their username, but they have the option to hide this.
						</p>
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
						<div class="invite-form crew-form">
							<input type="hidden" value="{{ csrf_token() }}" id="csrfToken">
							<input type="hidden" value="0" id="selectedGameId">
							<h3>
								I want to name my crew <input type="text" id="crewName" placeholder="NoodlesFTW" class="big-inline-input">. This crew is <select id="crewType">
																<option value="0">public</option>
																<option value="1">semi-private</option>
																<option value="2">private</option>
															</select>.
							</h3>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@stop