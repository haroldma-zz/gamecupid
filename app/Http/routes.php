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
	Route::get('/account/connect/xboxlive', 'PageController@connectXbox');
	Route::get('/account/connect/steam', 'PageController@connectSteam');


	// POST routes
    Route::post('account/connect/psn', 'PlatformValidatorController@validatePsn');

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