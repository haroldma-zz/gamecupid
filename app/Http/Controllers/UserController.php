<?php namespace App\Http\Controllers;

use Hash;
use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;

class UserController extends Controller {

	/**
	*
	* Register a user
	*
	**/
	public function register(RegisterFormRequest $request)
	{
		/**
		*
		* If the form validation is a success, create the user in the database.
		* See: App\Http\Requests\RegisterFormRequest
		*
		**/
		$user = new User;

		$user->first_name = $request->get('first_name');
		$user->last_name  = $request->get('last_name');
		$user->username   = $request->get('username');
		$user->email      = $request->get('email');
		$user->password   = Hash::make($request->get('password'));
		$user->banned     = false;
		$user->is_private = false;
		$user->dob        = date('Y-m-d', strtotime("now"));

		if ($user->save())
		{
			// Log the user in
			if (Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')]))
			{
				return redirect('/');
			}
			else
			{
				// Login failed, let the user log in manually
				return redirect('/login')->with('notice', ['info', 'You can now login with your e-mail address and password.']);
			}
		}
	}


	/**
	*
	* Log a user in
	*
	**/
	public function login(LoginFormRequest $request)
	{
		/**
		*
		* If form validation is a succes, attempt to login the user.
		*
		**/
		if (Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')]))
		{
			return redirect('/');
		}
		else
		{
			return redirect()->back()->withInput()->with('notice', ['error', 'Wrong e-mail/password combination.']);
		}
	}


	/**
	*
	* Log a user out
	*
	**/
	public function logout()
	{
		Auth::logout();
		return redirect('/');
	}


	/**
	*
	* Check for notifications
	*
	**/
	public function checkNotification(Request $request)
	{
		if (!$request->ajax())
		{
			return redirect('/');
		}

		return 1;
	}

}