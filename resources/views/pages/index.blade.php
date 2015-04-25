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
				Rep: {{ Auth::user()->rep() }}
			@else
				@foreach ($platforms as $platform)
					<p>{{ $platform->name }}</p>
                    <img src="{{ $platform->logo_url  }}" width="200" />
                    <p>{{ $platform->description  }}</p>

                    @foreach ($platform->consoles as $console)
                        <p>{{ $console->name }}</p>
                        <img src="{{ $console->logo_url  }}" width="200" />
                        <p>{{ $console->description  }}</p>
                    @endforeach
                    <hr />
				@endforeach
			@endif
		</div>
	</div>
</section>
@stop