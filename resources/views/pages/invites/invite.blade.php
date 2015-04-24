@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-12 columns">
			<div class="panel extra">
				<h2 class="light-header">
					Invite
				</h2>
				<hr>
				<div class="row">
					<div class="medium-8 medium-offset-2 columns">
						{!! Form::open(['url' => '', 'class' => 'form']) !!}
						{!! Form::label('game', 'I want to submit an invite for...') !!}
						{!! Form::select('game', [
												'0' => 'Select a game'
												 ],
												0,
												['class' => 'form-control']) !!}
						{!! Form::label('console', 'on the...') !!}
						{!! Form::select('console', ['0' => 'Select a console',
													 '1' => 'Xbox 360',
													 '2' => 'Xbox One',
													 '3' => 'Playstation 3',
													 '4' => 'Playstation 4',
													 '5' => 'PC'],
													0,['class' => 'form-control']) !!}

						<br><br>
						<button type="submit" class="btn big primary">Submit invite</button>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@stop