@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-6 medium-offset-3 columns">
			<div class="panel">
				<h3 class="mb">
					Login
				</h3>
				@if (Session::has('errors-for') && Session::get('errors-for') == 'login')
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
				{!! Form::open(['route' => 'user.login', 'class' => 'form']) !!}
				{!! Form::label('username', 'Username') !!}
				{!! Form::text('username', '', ['class' => 'form-control']) !!}
				{!! Form::label('password', 'Password') !!}
				{!! Form::password('password', ['class' => 'form-control']) !!}
				<br>
				<button type="submit" class="btn primary big">Login</button>
				{!! Form::close() !!}
				<hr>
				@if (Session::has('errors-for') && Session::get('errors-for') == 'register')
				<h3 class="pointer mb" id="registerFormBtn">
					...or register <i class="ion-ios-arrow-down ion-ios-arrow-up"></i>
				</h3>
				<ul class="no-bullet text-alert smaller-fs">
					@foreach($errors->all() as $error)
					<li>{{ $error }}</li>
					@endforeach
				</ul>
				<div class="register-form open" id="registerForm">
				@else
				<h3 class="pointer mb" id="registerFormBtn">
					...or register <i class="ion-ios-arrow-down"></i>
				</h3>
				<div class="register-form" id="registerForm">
				@endif
					{!! Form::open(['route' => 'user.register', 'class' => 'form']) !!}
					<div class="row">
						<div class="medium-6 columns">
							{!! Form::label('first_name', 'First name') !!}
							{!! Form::text('first_name', '', ['class' => 'form-control']) !!}
						</div>
						<div class="medium-6 columns">
							{!! Form::label('last_name', 'Last name') !!}
							{!! Form::text('last_name', '', ['class' => 'form-control']) !!}
						</div>
					</div>
					{!! Form::label('username', 'Choose a username. You use this to login.') !!}
					{!! Form::text('username', '', ['class' => 'form-control']) !!}
					{!! Form::label('email', 'Your e-mail address') !!}
					{!! Form::email('email', '', ['class' => 'form-control', 'placeholder' => 'noodles@example.com']) !!}
					{!! Form::label('password', 'Choose a password') !!}
					{!! Form::password('password', ['class' => 'form-control']) !!}
					<br>
					<button type="submit" class="btn primary big">Register</button>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</section>
@stop