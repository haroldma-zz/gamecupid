<?php namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Console;
use App\Models\Post;
use App\Enums\Categories;
use App\Enums\RequestStates;
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
	public function index(Request $request, $platform = null)
    {
		$category     = $request->input('category', false);
		$limit        = (int)$request->input('limit', 10);
		$after        = decodeHashId($request->input('after', 0));
		$fromTimezone = $request->input('ftz');
		$toTimezone   = $request->input('ttz');
		$useTimezone  = $fromTimezone != null;
		$t            = $request->input('t', 'day');
		$guardedLimit = min($limit, 100);
		$guardedLimit = max($guardedLimit, 1);
		$to           = Carbon::now();
		$from         = stringToFromDate($t);


        switch($platform)
        {
            case "psn":
                $platform = 2;
                break;
            case "xbl":
                $platform = 1;
                break;
            default:
                $platform = 0;
        }

        if ($category != false)
        {
            switch($category)
            {
                case "anytime":
                    $category = Categories::ANYTIME;
                    break;
                case "planned":
                    $category = Categories::PLANNED;
                    break;
                default:
                    $category = Categories::ASAP;
                    break;
            }
        }

        $query = "GetNewInvites($after, $guardedLimit)";
        if ($category != false){
            if ($platform > 0){
                $query = "GetNewInvitesByPlatformAndCategory($platform, $category, $after, $guardedLimit)";
            }
            else {
                $query = "GetNewInvitesByCategory($category, $after, $guardedLimit)";
            }
        }
        else if ($platform > 0) {
            $query = "GetNewInvitesByPlatform($platform, $after, $guardedLimit)";
        }
        $posts = Post::hydrateRaw("CALL " . $query);

        if ($request->ajax())
            return invitesToDtos($posts);


        # Only fetch bestGamers if $request is not ajax
        $bestGamers = User::bestGamers();


		return view('pages.index', ['posts' => $posts, 'bestGamers' => $bestGamers]);
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
		$notifications = Auth::user()->rNotifications()->orderBy('id', 'DESC')->get();

		return view('pages.notifications')->with(['notifications' => $notifications]);
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


    /**
    *
    * Game session page
    *
    **/
    public function gameSession($hashId, $slug)
    {
    	if (!Auth::check())
    		return redirect('/login');

		$id   = decodeHashId($hashId);
		$post = Post::find($id);

    	if (!$post)
    		return 'can\'t find that session';

		$participants   = [];
		$participants[] = $post->user->username;

    	foreach ($post->requests()->where('state', RequestStates::ACCEPTED)->get() as $request)
    	{
    		$participants[] = $request->user->username;
    	}

    	if (!in_array(Auth::user()->username, $participants))
    		return 'you\'re not allowed to view this page.';

    	return view()->make('pages.posts.detailpage')->with(['post' => $post]);
    }


    /**
    *
    * Blog page
    *
    **/
    public function blog()
    {
    	return view()->make('pages.blog.index');
    }

}





















