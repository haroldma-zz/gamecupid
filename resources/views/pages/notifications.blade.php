@extends('base.base')

@section('page')
<section class="page">
	<div class="row">
		<div class="medium-12 columns">
			<div class="panel extra">
				<div class="row">
					<div class="medium-8 medium-offset-2 columns">
						<h2 class="light-header">Notifications</h2>
						<hr>
						@foreach(Auth::user()->rNotifications()->orderBy('id', 'DESC')->get() as $n)
							<div class="notification-entry">
								<h5 class="text-primary {{ ($n->read == false ? 'bold' : '') }}">
									<a href="">{{ $n->title }}</a>
								</h5>
								<p>
									{{ $n->description }}
								</p>
								<ul class="inline-list">
									<li>
										<a id="markNotificationAsReadBtn" data-nid="{{ $n->id }}">mark as {{ ($n->read == true ? 'un' : '') }}read</a>
									</li>
								</ul>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<input type="hidden" id="csrftoken" value="{{ csrf_token() }}">
@stop