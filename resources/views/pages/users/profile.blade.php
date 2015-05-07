@extends('base.base')

@section('page')
<section class="page">
	<div class="row medium-collapse detailpage">
		<div class="medium-10 medium-offset-1 columns">
			<div class="panel extra text-center">
				<h4>
					{{ $user->username }}'s profile
				</h4>
				<header>
					<div class="profile-picture" style="background-image:url({{ Gravatar::get($user->email) }});"></div>
				</header>
				<section>
					<ul class="no-bullet">
						<li><a href="">Add as friend</a></li>
						<li><a href="">Send message</a></li>
					</ul>
				</section>
				<hr>
				<footer>
					<h5 class="super-header">Achievements</h5>
					<br>
					@if ($user->profiles()->count() > 0)
						@foreach($user->profiles as $profile)
						<img src="{{ url('img/badges/' . $profile->platform->shortname) . '.png' }}">
						@endforeach
					@else
					<p>
						{{ $user->username }} has no achievements yet.
					</p>
					@endif
				</footer>
			</div>
			<div class="panel extra">

			</div>
		</div>
	</div>
</section>
@stop