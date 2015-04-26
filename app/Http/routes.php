<?php

/*
|--------------------------------------------------------------------------
| Schema builder route
|--------------------------------------------------------------------------
|
*/
// Visit this route once to generate the tables in our db.
// See App/Http/Controllers/TempController@makedb

Route::get('/makedb', 'TempController@makedb');


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*
* Non authenticated routes
*
**/

// GET routes
Route::get('/', 'PageController@index');
Route::get('/login', 'PageController@login');


// POST routes
Route::post('/login', ['as' => 'user.login', 'uses' => 'UserController@login']);
Route::post('/register', ['as' => 'user.register', 'uses' => 'UserController@register']);


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

	Route::get('/account', 'PageController@account');
	Route::get('/account/connect/psn', 'PageController@connectPsn');
	Route::get('/account/connect/xbl', 'PageController@connectXbl');
	Route::get('/account/connect/steam', 'PageController@connectSteam');
	Route::get('/account/disconnect/{platform}/{username}', 'ProfileController@disconnect');

	Route::get('/notification', 'UserController@checkNotification');
	Route::get('/notifications', 'PageController@notifications');

	Route::get('/invite', 'PageController@invite');


	// POST routes
    Route::post('account/connect/psn', 'PlatformValidatorController@validatePsn');
    Route::post('account/connect/xbl', 'PlatformValidatorController@validateXbl');
    Route::post('account/connect/steam', 'PlatformValidatorController@validateSteam');

    Route::post('/markasread', 'UserController@markNotificationAsRead');

    Route::post('/invite', 'InviteController@invite');

});



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