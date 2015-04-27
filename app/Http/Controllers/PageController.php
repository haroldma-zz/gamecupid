<?php namespace App\Http\Controllers;

use App\Models\Console;
use App\Models\Invite;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;

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
        $pageSize = 10;
        $page = $request->input('page', 1);

        if (!is_int($page))
            $page = 1;

        $page = ($page - 1) * $pageSize;
        $pageEnd = $pageSize;

        $sort = $request->input('sort', 'hot');
        $sqlFunction = "calculateHotness(getInviteUpvotes(id), getInviteDownvotes(id), created_at)";

        if ($sort == "controversial")
            $sqlFunction = "calculateControversy(getInviteUpvotes(id), getInviteDownvotes(id))";

        $query = "SELECT *, $sqlFunction as sort FROM invites
                  ORDER BY sort DESC LIMIT $page, $pageEnd;";

        if ($sort == "new")
            $query = "SELECT * FROM invites
                  ORDER BY created_at DESC LIMIT $page, $pageEnd;";
        else if ($sort == "top")
            $query = "SELECT *, getInviteUpvotes(id) as upvotes, getInviteDownvotes(id) as downvotes FROM invites
                  ORDER BY upvotes - downvotes DESC LIMIT $page, $pageEnd;";

        $invites = Invite::hydrateRaw($query);
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
		$invite = Invite::find(Hashids::decode($hashid));

		if (!$invite)
			return redirect('/page-not-found');

		return view('pages.invites.detailpage', ['invite' => $invite[0]]);
	}


}
