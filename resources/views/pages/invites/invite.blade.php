@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-12 columns">
			<div class="panel extra">
				<div class="row">
					<div class="medium-8 medium-offset-2 columns">
						<h2 class="light-header">
							Invite
						</h3>
						<hr>
						<p>
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam, a, ea voluptate nemo asperiores cum ullam commodi neque voluptatem! Aut beatae delectus neque, assumenda ratione, odit possimus mollitia eum, ipsum dolor error.
						</p>
						<br>
						{!! Form::open(['url' => '/invite', 'class' => 'form']) !!}
							{!! Form::label('console_id', 'I want to submit an invite for...') !!}
							{!! Form::select('console_id', $consoleSelections,
							0,['class' => 'form-control']) !!}
                            {!! Form::label('game_id', 'for the game..') !!}
                            {!! Form::select('game_id', [
                            '0' => 'Select a game'
                            ],
                            0,
                            ['class' => 'form-control']) !!}
                            {!! Form::label('title', 'Title') !!}
                            {!! Form::text('title', '', ['form-control']) !!}
                            {!! Form::label('self_text', 'Description') !!}
                            {!! Form::textarea('self_text', '', ['form-control']) !!}
                            {!! Form::checkbox('requires_approval', 1) !!}
                            {!! Form::label('requires_approval', 'When someone wants to play with me, that person requires my approval.') !!}

                            <br><br><br>
						    {!! Recaptcha::render() !!}
						    <br>
						    <button type="submit" class="btn big primary">Submit invite</button>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@stop