<?php namespace App\Http\Controllers;

use App\Models\Console;
use App\Models\Invite;

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
	public function index()
	{
        $query = "SELECT *, calculateHotness(getInviteUpvotes(id), getInviteDownvotes(id), created_at) as hotness FROM invites ORDER BY hotness DESC;";
        $invites = Invite::hydrateRaw($query)->take(10);
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
	* Invite page
	*
	**/
	public function invite()
	{
		$consoles = Console::all();

        $consoleSelections = ['0' => 'Select a console'];
        foreach ($consoles as $console)
        {
            $consoleSelections[] = $console->name;
        }

		return view('pages.invites.invite', [ 'consoleSelections' => $consoleSelections]);
	}


}
