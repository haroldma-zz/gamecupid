@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-8 medium-offset-2 columns">
			<div class="row">
				<div class="medium-6 columns">
					<div class="panel">
						<h5>
							Connect
						</h5>
						<hr>
						<p>
							<ul class="no-bullet">
								<li><a href="{!! url('/account/connect/psn') !!}">Playstation Network</a></li>
								<li><a href="{!! url('/account/connect/xbl') !!}">Xbox Live</a></li>
								<li><a href="{!! url('/account/connect/steam') !!}">Steam</a></li>
							</ul>
						</p>
					</div>
				</div>
				<div class="medium-6 columns">
					<div class="panel">
						<h5>
							Account
						</h5>
						<hr>
						<p>
							<ul class="no-bullet">
								<li><a href="">Edit profile</a></li>
								<li><a href="">Change timezone</a></li>
								<li><a href="">Change password</a></li>
							</ul>
						</p>
					</div>
				</div>
			</div>
			<div class="panel">
				<h5>
					Connected network profiles ({{ Auth::user()->profiles()->count() }})
				</h5>
				<hr>
                @if (Session::has('notice'))
                    @if (Session::get('notice')[0] == 'error')
                        <ul class="no-bullet text-alert smaller-fs">
                    @elseif (Session::get('notice')[0] == 'info')
                        <ul class="no-bullet text-info smaller-fs">
                    @elseif (Session::get('notice')[0] == 'success')
                    	<ul class="no-bullet text-success smaller-fs">
                    @endif
                    <li>{{ Session::get('notice')[1] }}</li>
                    </ul>
                @endif
				@if(Auth::user()->profiles()->count() === 0)
				<p>
					You don't have any online gaming profiles connected to this account.
					<br><br><br>
					Connect your online gaming profiles by clicking on a platform under 'Connect'.
				</p>
				@else
				<div class="row">
                    @foreach (Auth::user()->profiles as $profile)
                    <div class="medium-4 columns end">
						<div class="profile">
                            <img src="{{ $profile->platform->logo_url  }}" />
                            <ul class="inline-list profiles-mini-list">
                            	<li>{{ $profile->online_username }}</li>
                            	<li><a href="/account/disconnect/{{ $profile->platform->shortname }}/{{ $profile->online_username }}" id="openDialog"
                            		data-type="confirm"
                            		data-message="Are you sure you want to disconnect {{ $profile->online_username }}?"
                            		>disconnect</a></li>
                            </ul>
						</div>
					</div>
                    @endforeach
				</div>
				@endif
			</div>
		</div>
	</div>
</section>
@stop