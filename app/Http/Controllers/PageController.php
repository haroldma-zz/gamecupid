<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Console;
use App\Models\Post;
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
		$limit        = (int)$request->input('limit', 10);
		$fromTimezone = $request->input('ftz');
		$toTimezone   = $request->input('ttz');
		$useTimezone  = $fromTimezone != null;

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

        $query = $useTimezone ? "GetHotPostsByTimezone($after, $guardedLimit, $fromTimezone, $toTimezone)"
            : "GetHotPosts($after, $guardedLimit)";

        if ($sort == "controversial")
            $query = $useTimezone ? "GetControversialPostsByTimezone($after, $guardedLimit, '$from', '$to', $fromTimezone, $toTimezone)"
                : "GetControversialPosts($after, $guardedLimit, '$from', '$to')";
        else if ($sort == "top")
            $query = $useTimezone ? "GetTopPostsByTimezone($after, $guardedLimit, '$from', '$to', $fromTimezone, $toTimezone)"
            : "GetTopPosts($after, $guardedLimit, '$from', '$to')";
        else if ($sort == "new")
            $query = $useTimezone ? "GetNewPostsByTimezone($after, $guardedLimit, $fromTimezone, $toTimezone)"
            : "GetNewPosts($after, $guardedLimit)";


        $posts = Post::hydrateRaw('CALL ' . $query);

        $topPlayers = User::topPlayers();

        if ($request->ajax())
            return invitesToDtos($posts);

		return view('pages.index', ['posts' => $posts, 'topPlayers' => $topPlayers]);
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
	* Post Form page
	*
	**/
	public function postForm()
	{
		$consoles = Console::all();

        $consoleSelections = ['0' => 'Select a console'];
        foreach ($consoles as $console)
        {
            $consoleSelections[] = $console->name;
        }

		return view('pages.posts.form', [ 'consoleSelections' => $consoleSelections]);
	}


	/**
	*
	* Post details page
	*
	**/
	public function post($hashid, $slug)
	{
		$post = Post::find(decodeHashId($hashid));

		if (!$post)
			return redirect('/page-not-found');

		return view('pages.posts.detailpage', ['post' => $post]);
	}

    /**
     *
     * Post details page
     *
     **/
    public function postWithContext(Request $request, $hashid, $slug, $context)
    {
        $post = Post::find(decodeHashId($hashid));

        if (!$post)
            return redirect('/page-not-found');

        $comment = Comment::find(decodeHashId($context));

        if (!$comment)
            return redirect('/page-not-found');

        $context = max((int)$request->input("context", 0), 0);

        return view('pages.posts.detailpage', ['post' => $post, 'context' => $context, 'comment' => $comment]);
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

}
