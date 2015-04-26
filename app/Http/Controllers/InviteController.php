<?php namespace App\Http\Controllers;

use Auth;
use App\Models\Rep;
use App\Models\Invite;
use App\Models\Notification;
use App\Http\Requests\InviteFormRequest;
use Cocur\Slugify\Slugify;

class InviteController extends Controller {

	/**
	*
	* New an invite
	*
	**/
	public function invite(InviteFormRequest $request)
	{
        $slugify = new Slugify();
        $slugify->addRule('+', 'plus');

		$invite                    = new Invite;
		$invite->title             = $request->get('title');
		$invite->slug              = $slugify->slugify($request->get('title'), "-");
		$invite->self_text         = $request->get('self_text');
		$invite->tag_text          = '-';
		$invite->player_count      = $request->get('player_count');
		$invite->requires_approval = ($request->get('requires_approval') == '' ? false : true);
		$invite->console_id        = $request->get('console_id');
		$invite->game_id           = $request->get('game_id');
		$invite->user_id           = Auth::user()->id;

		if ($invite->save())
		{
			$rep               = new Rep;
			$rep->rep_event_id = 6;  					// #6 of rep_events in db
			$rep->user_id      = Auth::user()->id;
			$rep->save();

			$not              = new Notification;
			$not->title       = "+1 rep"; 					// double check in db
			$not->description = "Submitted a invite.";  // double check in db
			$not->to_id       = Auth::user()->id;
			$not->save();

			return redirect('/');
		}
		else
		{
			return redirect()->back()->with('notice', ['error', 'Something went wrong... Try again.']);
		}
	}

}