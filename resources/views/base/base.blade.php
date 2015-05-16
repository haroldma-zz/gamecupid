<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>playddit</title>
	{!! HTML::style('stylesheets/app.css') !!}
	@yield('head')
</head>
<body>
	<nav class="topnav">
		<div class="row">
			<div class="medium-6 columns">
				<a class="brand" href="{!! url('/') !!}">playddit</a>
				<div class="platforms-container">
					<a id="feedSelector">Platforms <i class="ion-arrow-down-b"></i></a>
					<div class="platforms-list" id="platformList">
						<div>
							<ul class="no-bullet">
								<a href="{!! url('/psn') !!}"><li>Playstation Network</li></a>
								<a href="{!! url('/xbl') !!}"><li>Xbox Live</li></a>
								<a href="{!! url('/steam') !!}"><li>Steam</li></a>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="medium-6 columns text-right">
				@if(Auth::check())
				<a href="{!! url('/post') !!}"><i class="ion-plus"></i></a>
				<a href="{!! url('/notifications') !!}" id="notificationsLink">
					@if(Auth::user()->rNotifications()->where('read', false)->count() - Auth::user()->rNotifications()->where('notified', false)->count() > 0)
					<i class="ion-android-notifications orange-text" id="not-icon"></i>
					<span class="orange-text" id="u-not-read-count">{{ Auth::user()->rNotifications()->where('read', false)->count() - Auth::user()->rNotifications()->where('notified', false)->count() }}</span>
					@else
					<i class="ion-android-notifications-none" id="not-icon"></i>
					<span class="orange-text" id="u-not-read-count"></span>
					@endif
				</a>
				<a href="{!! url('/gamer/' . Auth::user()->username) !!}"><span class="hide-for-small">{{ Auth::user()->username }}</span><span class="show-for-small"><i class="ion-person"></i></span> <span class="header-rep-count"><b>{{ Auth::user()->level() }}</b>:{{ Auth::user()->rep() }}</span></a>
				<a href="{!! url('/settings') !!}">Settings</a>
				<a href="{!! url('/logout') !!}"><i class="ion-power"></i></a>
				@else
				<a href="{!! url('/login') !!}">Login / Register</a>
				@endif
			</div>
		</div>
	</nav>

	@yield('page')

	<footer class="footer" id="footer">
		<div class="row">
			<div class="medium-12 columns">
				<h5 class="super-header">Playddit</h5>
				<br><br>
				<script type="text/javascript" src="//www.redditstatic.com/button/button1.js"></script>
			</div>
		</div>
	</footer>

	{!! HTML::script('bower_components/jquery/dist/jquery.min.js') !!}
	{!! HTML::script('js/moment.js') !!}
	{!! HTML::script('js/livestamp.js') !!}
	{!! HTML::script('js/app.js') !!}
	{!! HTML::script('js/voter.js') !!}
	@if(Auth::check())
	{!! HTML::script('js/ion.sound.min.js') !!}
	{!! HTML::script('js/notifier.js') !!}
	@endif
	@yield('scripts')
</body>
</html>