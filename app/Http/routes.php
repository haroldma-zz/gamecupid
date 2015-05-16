<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*
* Non authenticated routes
*
**/

// GET routes
Route::get('/login', 'PageController@login');

# '/' route is at the bottom


# Posts
Route::get('/post/{hashid}/{slug}', 'PageController@post');
Route::get('/post/{hashid}/{slug}/request-invite', 'PostController@requestInvite');
Route::get('/post/{hashid}/{slug}/{context}', 'PageController@postWithContext');


Route::get('/game/consoles', 'GameController@formConsoles');


# Gamer profiles
Route::get('/g/{username}', function($username) {
    return redirect("/gamer/$username", 301);
});
Route::get('/gamer/{username}', 'PageController@userProfile');



// POST routes
Route::post('/login', ['as' => 'user.login', 'uses' => 'UserController@login']);
Route::post('/register', ['as' => 'user.register', 'uses' => 'UserController@register']);
Route::post('/game/search', 'GameController@search');

Route::post('/post/upvote', 'PostController@upvote');
Route::post('/post/downvote', 'PostController@downvote');
Route::post('/post/{hashid}/{slug}', 'PostController@comment');

Route::post('/comment/upvote', 'CommentController@upvote');
Route::post('/comment/downvote', 'CommentController@downvote');

/**
*
* User authenticated routes
*
**/
Route::group(['middleware' => 'auth'], function()
{
	// Routes that should only be accesible if the user has successfully
	// logged in. If the user is not logged in, we redirect him to the
	// login page. We can control this behaviour in
	// App\Http\Middleware\Authenticate


	// GET routes
	Route::get('/logout', 'UserController@logout');

	Route::get('/settings', 'PageController@settings');
	Route::get('/account/connect/psn', 'PageController@connectPsn');
	Route::get('/account/connect/xbl', 'PageController@connectXbl');
	Route::get('/account/connect/steam', 'PageController@connectSteam');
	Route::get('/account/disconnect/{platform}/{username}', 'ProfileController@disconnect');

	Route::get('/notification', 'UserController@checkNotification');
	Route::get('/notifications', 'PageController@notifications');

	Route::get('/post', 'PageController@postForm');

	Route::get('/{username}/session/{request_id}/accept', 'SessionController@acceptInviteRequest');
	Route::get('/{username}/session/{request_id}/decline', 'SessionController@declineInviteRequest');
	Route::get('/{username}/session/{session_id}', 'PageController@gameSession');


	// POST routes
    Route::post('account/connect/psn', 'PlatformValidatorController@validatePsn');
    Route::post('account/connect/xbl', 'PlatformValidatorController@validateXbl');
    Route::post('account/connect/steam', 'PlatformValidatorController@validateSteam');

    Route::post('/markasread', 'UserController@markNotificationAsRead');

    Route::post('/post', 'PostController@post');
});

Route::get('/{platform?}', 'PageController@index');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| The following routes are API related
|
*/
Route::group(['prefix' => 'api'], function()
{
    Route::get('user/{username}', function()
    {
        // Matches "/api/user/noodles_ftw" URL
    });
});