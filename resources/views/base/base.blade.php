<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GameCupid</title>
	{!! HTML::style('stylesheets/app.css') !!}
</head>
<body>
	<nav class="topnav">
		<div class="row">
			<div class="medium-12 columns">
				<div class="left">
					<a class="brand" href="{!! url('/') !!}">gamecupid</a>
				</div>
				<div class="right">
					@if(Auth::check())
					<a href="{!! url('/invite') !!}"><i class="ion-plus"></i></a>
					<a href="{!! url('/notifications') !!}" id="notificationsLink">
						@if(Auth::user()->rNotifications()->where('read', false)->count() > 0)
						<i class="ion-android-notifications orange-text" id="not-icon"></i>
						<span class="orange-text" id="u-not-read-count">{{ Auth::user()->rNotifications()->where('read', false)->count() }}</span>
						@else
						<i class="ion-android-notifications-none" id="not-icon"></i>
						<span class="orange-text" id="u-not-read-count"></span>
						@endif
					</a>
					<a href="{!! url('/account') !!}"><span class="hide-for-small">{{ Auth::user()->username }}</span><span class="show-for-small"><i class="ion-person"></i></span> <span class="header-rep-count">{{ Auth::user()->rep() }}</span></a>
					<a href="{!! url('/logout') !!}"><i class="ion-power"></i></a>
					@else
					<a href="{!! url('/login') !!}">Login / Register</a>
					@endif
				</div>
			</div>
		</div>
	</nav>

	@yield('page')

	<footer class="footer"></footer>

	{!! HTML::script('bower_components/jquery/dist/jquery.min.js') !!}
	{!! HTML::script('js/app.js') !!}
	@if(Auth::check())
	{!! HTML::script('js/notifier.js') !!}
	@endif
</body>
</html>