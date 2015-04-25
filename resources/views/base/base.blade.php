<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Gamebros</title>
	{!! HTML::style('stylesheets/app.css') !!}
</head>
<body>
	<nav class="topnav">
		<div class="row">
			<div class="medium-12 columns">
				<div class="left">
					<a class="brand" href="{!! url('/') !!}">gamebros</a>
				</div>
				<div class="right">
					@if(Auth::check())
					<a href="{!! url('/invite') !!}"><i class="ion-plus"></i></a>
					<a href="{!! url('/account') !!}"><b>{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}</b></a>
					<a href="{!! url('/logout') !!}">Logout</a>
					@else
					<a href="{!! url('/login') !!}">Login / Register</a>
					@endif
				</div>
			</div>
		</div>
	</nav>

	@yield('page')

	<div class="notification">
		<h6>
			Invite from noodles_ftw<br>
			<small>Call of Duty: Advanced Warfare</small>
			<div class="closer" onclick="closeSuperParent(this);">
				<i class="ion-close"></i>
			</div>
		</h6>
	</div>
	<footer class="footer"></footer>

	{!! HTML::script('bower_components/jquery/dist/jquery.min.js') !!}
	{!! HTML::script('js/app.js') !!}
	@if(Auth::check())
	{!! HTML::script('js/notifier.js') !!}
	@endif
</body>
</html>