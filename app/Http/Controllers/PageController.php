<?php namespace App\Http\Controllers;

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
		$after    = decodeHashId($request->input('after', 0));
        $sort     = $request->input('sort', 'hot');
        $t     = $request->input('t', 'day');

        $guardedLimit = min($limit, 100);
        $guardedLimit = max($guardedLimit, 1);

        $to   = Carbon::now();
        $from = stringToFromDate($t);

        $query = "GetHotInvites($after, $guardedLimit)";

        if ($sort == "controversial")
            $query = "GetControversialInvites($after, $guardedLimit, '$from', '$to')";
        else if ($sort == "top")
            $query = "GetTopInvites($after, $guardedLimit, '$from', '$to')";
        else if ($sort == "new")
            $query = "GetNewInvites($after, $guardedLimit)";


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
	* User account page
	*
	**/
	public function account()
	{
		return view('pages.account');
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
    public function inviteWithContext($hashid, $slug, $context)
    {
        $invite = Invite::find(decodeHashId($hashid));

        if (!$invite)
            return redirect('/page-not-found');

        $context = Comment::find(decodeHashId($context));

        if (!$context)
            return redirect('/page-not-found');

        return view('pages.invites.detailpage', ['invite' => $invite, 'context' => $context]);
    }


}
