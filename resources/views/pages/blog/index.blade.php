@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-10 medium-offset-1 columns">
			<div class="panel">
				<h3>
					Welcome to playddit.
					<br>
					<small>
						posted by <a href="{{ url('/gamer/noodles_ftw') }}">noodles_ftw</a> on <time datetime="2011-01-12">January 12th, 2011</time>
					</small>
				</h3>
				<br>
				<div class="panel markdown-text">
					<h1>
						Intro
					</h1>
					<p>
						playddit.com is a platform to setup online gameplay with other gamers. We aim to provide a platform that meets these principles:
					</p>
					<h3>
						Quick & Easy
					</h3>
					<p>
						...
					</p>
					<h3>
						Something else
					</h3>
					<p>
						...
					</p>
					<h3>
						User driven development
					</h3>
					<p>
						The platform is in constant development; we fix bugs and implement new features day in and out. We listen to our users to give them the best experience on the platform possible. We discuss features you suggest and implement those which get the most support of other users.
					</p>
					<br>
					<br>
					<h1>
						Beta
					</h1>
					<p>
						We are still in Beta and accepting applications for beta testing, apply <a href="{{ url('/beta/apply') }}">here</a>.
					</p>
					<br>
					<br>
					<h1>
						Implemented features
					</h1>
					<ul>
						<li><a href="http://igdb.com/" target="_blank">IGDB</a>'s API to fetch games</li>
						<li>Playstation Network support</li>
						<li>Xbox Live support</li>
						<li>Planned, As Soon As Possible and Anytime submissions</li>
						<li>Threaded comments</li>
						<li>Rep</li>
						<li>Notifications system</li>
						<li>User profile page</li>
						<li>Timezone filtering [suggested by <a href="http://www.reddit.com/r/gaming/comments/34m4dc/a_week_ago_i_posted_about_a_platform_dedicated_to/cqw2j6e?context=3">/u/Bust_em</a> (on reddit)]</li>
						<li>Platform filtering</li>
						<li>Markdown</li>
						<li>Emoji's</li>
					</ul>
					<h1>
						Planned features
					</h1>
					<ul>
						<li>Steam support</li>
						<li>Console filtering</li>
						<li>Game filtering</li>
						<li>Friending system</li>
						<li>Crews [suggested by <a href="http://www.reddit.com/r/gaming/comments/32wy63/something_im_working_on_to_connect_gamers/cqfrgiq">/u/jbridgiee</a> (on reddit)]</li>
						<li>Tournament submissions</li>
						<li>Challenge submissions</li>
						<li>Bet submissions</li>
					</ul>
					<h1>
						Suggested features (by users)
					</h1>
					<ul>
						<li>None at the moment</li>
					</ul>
				</div>
			</div>
			<div class="panel">
				<h6>Comments</h6>
			</div>
		</div>
	</div>
</section>
@stop