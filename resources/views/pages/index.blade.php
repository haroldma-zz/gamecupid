@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-12 columns">
			@if(Auth::check())
				Profiles: {{ Auth::user()->profiles()->count() }}
				<br>
				Invites: {{ Auth::user()->invites()->count() }}
				<br>
				Accepts: {{ Auth::user()->accepts()->count() }}
				<br>
			@endif
		</div>
	</div>
</section>
@stop