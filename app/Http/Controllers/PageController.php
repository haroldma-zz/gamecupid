<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Console;
use App\Models\Invite;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Comment;

class PageController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Page Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles all page routes. All views are located in
	| resources/views
	|
	*/

	/**
	*
	* Show the application's index page
	*
	**/
	public function index(Request $request)
    {
		$limit    = (int)$request->input('limit', 10);
		$fromTimezone  = $request->input('ftz');
        $toTimezone    = $request->input('ttz');
        $useTimezone   = $fromTimezone != null;

        if ($useTimezone)
        {
            // the smallest timezone -12 and biggest +14
            $fromTimezone = max((int)$fromTimezone, hourToMinute(-12));
            $fromTimezone = min($fromTimezone, hourToMinute(14));
            $toTimezone = max((int)$toTimezone, $fromTimezone);
            $toTimezone = min($toTimezone, hourToMinute(14));
        }

		$after    = decodeHashId($request->input('after', 0));
        $sort     = $request->input('sort', 'hot');
        $t     = $request->input('t', 'day');

        $guardedLimit = min($limit, 100);
        $guardedLimit = max($guardedLimit, 1);

        $to   = Carbon::now();
        $from = stringToFromDate($t);

        $query = $useTimezone ? "GetHotInvitesByTimezone($after, $guardedLimit, $fromTimezone, $toTimezone)"
            : "GetHotInvites($after, $guardedLimit)";

        if ($sort == "controversial")
            $query = $useTimezone ? "GetControversialInvitesByTimezone($after, $guardedLimit, '$from', '$to', $fromTimezone, $toTimezone)"
                : "GetControversialInvites($after, $guardedLimit, '$from', '$to')";
        else if ($sort == "top")
            $query = $useTimezone ? "GetTopInvitesByTimezone($after, $guardedLimit, '$from', '$to', $fromTimezone, $toTimezone)"
            : "GetTopInvites($after, $guardedLimit, '$from', '$to')";
        else if ($sort == "new")
            $query = $useTimezone ? "GetNewInvitesByTimezone($after, $guardedLimit, $fromTimezone, $toTimezone)"
            : "GetNewInvites($after, $guardedLimit)";


        $invites = Invite::hydrateRaw('CALL ' . $query);

        if ($request->ajax())
            return invitesToDtos($invites);

		return view('pages.index', ['invites' => $invites]);
	}


	/**
	*
	* The login / register page
	*
	**/
	public function login()
	{
		return view('pages.login');
	}


	/**
	*
	* User settings page
	*
	**/
	public function settings()
	{
		return view('pages.users.settings');
	}


	/**
	*
	* Notifications page
	*
	**/
	public function notifications()
	{
		return view('pages.notifications');
	}


	/**
	*
	* Connect PSN page
	*
	**/
	public function connectPsn()
	{
		return view('pages.connect.psn');
	}


	/**
	*
	* Connect XBL page
	*
	**/
	public function connectXbl()
	{
		return view('pages.connect.xbl');
	}


	/**
	*
	* Connect Steam page
	*
	**/
	public function connectSteam()
	{
		return view('pages.connect.steam');
	}


	/**
	*
	* Invite Form page
	*
	**/
	public function inviteForm()
	{
		$consoles = Console::all();

        $consoleSelections = ['0' => 'Select a console'];
        foreach ($consoles as $console)
        {
            $consoleSelections[] = $console->name;
        }

		return view('pages.invites.invite', [ 'consoleSelections' => $consoleSelections]);
	}


	/**
	*
	* Invite details page
	*
	**/
	public function invite($hashid, $slug)
	{
		$invite = Invite::find(decodeHashId($hashid));

		if (!$invite)
			return redirect('/page-not-found');

		return view('pages.invites.detailpage', ['invite' => $invite]);
	}

    /**
     *
     * Invite details page
     *
     **/
    public function inviteWithContext(Request $request, $hashid, $slug, $context)
    {
        $invite = Invite::find(decodeHashId($hashid));

        if (!$invite)
            return redirect('/page-not-found');

        $comment = Comment::find(decodeHashId($context));

        if (!$comment)
            return redirect('/page-not-found');

        $context = max((int)$request->input("context", 0), 0);

        return view('pages.invites.detailpage', ['invite' => $invite, 'context' => $context, 'comment' => $comment]);
    }


    /**
    *
    * User profile page
    *
    **/
    public function userProfile($username)
    {
    	$user = User::where('username', $username)->first();

    	if (!$user)
    		return redirect('/gamer-not-found');

    	return view('pages.users.profile', ['user' => $user]);
    }


    /**
    *
    * Crew page
    *
    **/
    public function crewPage($crewname)
    {
    	return view('pages.crews.crew');
    }


    /**
    *
    * Crew form
    *
    **/
    public function crewForm()
    {
    	return view('pages.crews.create');
    }


}
