@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-12 columns">
			<div class="panel">
				<div class="row">
                    @if (Session::has('notice'))
                        @if (Session::get('notice')[0] == 'error')
                            <ul class="no-bullet text-alert smaller-fs">
                        @else
                            <ul class="no-bullet smaller-fs">
                        @endif
                        <li>{{ Session::get('notice')[1] }}</li>
                        </ul>
                     @endif
					<div class="medium-6 columns">
						<h4>
							Connected profiles ({{ Auth::user()->profiles()->count() }})
						</h4>
						<hr>
						@if(Auth::user()->profiles()->count() === 0)
						<p>
							You don't have any online gaming profiles connected to this account.
							<br><br><br>
							Connect your online gaming profiles by clicking on a platform under 'Connect'.
						</p>
						@else
                            @foreach (Auth::user()->profiles as $profile)
                                <p>{{ $profile->online_id }}</p>
                                <img src="{{ $profile->platform->logo_url  }}" width="200" />
                            @endforeach
						@endif
					</div>
					<div class="medium-3 columns">
						<h4>
							Connect
						</h4>
						<hr>
						<p>
							<ul class="no-bullet">
								<li><a href="{!! url('/account/connect/psn') !!}">Playstation Network</a></li>
								<li><a href="{!! url('/account/connect/xbl') !!}">Xbox Live</a></li>
								<li><a href="{!! url('/account/connect/steam') !!}">Steam</a></li>
							</ul>
						</p>
					</div>
					<div class="medium-3 columns">
						<h4>
							Privacy
						</h4>
						<hr>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@stop