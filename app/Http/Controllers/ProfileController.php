<?php namespace App\Http\Controllers;

use Auth;

class ProfileController extends Controller {

	/**
	*
	* Disconnect a profile
	*
	**/
	public function disconnect($platform, $username)
	{
		($platform == 'psn' ? $platformId = 2 : $platformId = 1);

		$profile = Auth::user()->profiles()->where('platform_id', '=', $platformId)->where('online_username', '=', $username, 'AND')->first();

		if ($profile)
		{
			$profile->delete();
			return redirect()->back()->with('notice', ['success', 'Your profile has been disconnected.']);
		}
		else
		{
			return redirect()->back()->with('notice', ['error', 'You don\'t have ' . $username . ' connected with that platform.']);
		}
	}

}