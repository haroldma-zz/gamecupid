@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-9 columns">
			<div class="panel">
				<h1>PROFILE OF {{ $user->username }}</h1>
			</div>
		</div>
		<div class="medium-3 columns">
			<div class="panel">
				<h5 class="super-header">Achievements</h5>
			</div>
		</div>
	</div>
</section>
@stop