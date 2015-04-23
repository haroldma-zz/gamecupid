@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-8 medium-offset-2 columns">
			<div class="panel">
				<h3>
					Login to Xbox Live
				</h3>
				<br>
				@if (isset($error))
					<ul class="no-bullet text-alert smaller-fs">
						<li>{{ $error }}</li>
					</ul>
				@endif
				<p>
					Some text here to explain to the user that user needs to input the login details for XBL. If user doesn't have an XBL account user should create one first.
				</p>
				<br>
				@if(count($errors) > 0)
				<ul class="no-bullet text-alert">
					@foreach($errors->all() as $error)
					<li>{{ $error }}</li>
					@endforeach
				</ul>
				<br>
				@endif
				{!! Form::open(['url' => '/account/connect/xbl', 'class' => 'form']) !!}
				{!! Form::label('email', 'E-mail') !!}
				{!! Form::email('email', '', ['class' => 'form-control']) !!}
				{!! Form::label('password', 'Password') !!}
				{!! Form::password('password', ['class' => 'form-control']) !!}
				<br>
				<button type="submit" class="btn primary big">Connect to XBL</button>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</section>
@stop