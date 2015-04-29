<?php namespace App\Http\Controllers;

use App\Models\Console;
use App\Models\Invite;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Kumuwai\DataTransferObject\Laravel5DTO;
use Illuminate\Database\Eloquent\Collection;

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

        $guardedLimit = min($limit, 100);
        $guardedLimit = max($guardedLimit, 0);

        $time = array(
            Carbon::now()->subDays(5),
            Carbon::now()
        );


        $query = "CALL GetHotInvites($after, $guardedLimit);";

        if ($sort == "controversial")
            $query = "CALL GetControversialInvites($after, $guardedLimit, '$time[0]', '$time[1]');";
        else if ($sort == "top")
            $query = "call GetTopInvites($after, $guardedLimit, '$time[0]', '$time[1]');";
        else if ($sort == "new")
            $query = "call GetNewInvites($after, $guardedLimit);";


        $invites = Invite::hydrateRaw($query);

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


}
