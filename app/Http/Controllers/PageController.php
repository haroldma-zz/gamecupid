<?php namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\Console;

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
		$platforms = Platform::all();
		return view('pages.index', [ 'platforms' => $platforms]);
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
