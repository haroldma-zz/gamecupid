<?php namespace App\Http\Controllers;

use Hash;
use Auth;
use App\Models\Rep;
use App\Models\User;
use App\Models\Notification;
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
			// Give the user +1 rep
			$rep          = new Rep;
			$rep->amount  = 1;
			$rep->event   = "Became member of GameCupid.";
			$rep->user_id = $user->id;
			$rep->save();

			// Rep notification
			$notification              = new Notification;
			$notification->title       = "+1 rep";
			$notification->description = $rep->event;
			$notification->to_id       = $user->id;
			$notification->from_id     = 0;
			$notification->read        = false;
			$notification->notified    = false;
			$notification->save();

			// Log the user in
			if (Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')]))
			{
				return redirect('/');
			}
			else
			{
				// Login failed, let the user log in manually
				return redirect('/login')->with('notice', ['info', 'You can now login with your username and password.']);
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
			return redirect('/notifications');
		}

		$n     = Auth::user()->rNotifications()->where('notified', false)->orderBy('id', 'DESC')->first();
		$check = (isset($n->notified) ? $n->notified : false);

		while ($check === false) {
			sleep(5);

			$n     = Auth::user()->rNotifications()->where('notified', false)->orderBy('id', 'DESC')->first();
			$check = (isset($n->notified) ? $n->notified : false);
		}

		$n->notified = true;
		$n->save();

		return response()->json($n);
	}


	/**
	*
	* Mark a notification as read
	*
	**/
	public function markNotificationAsRead(Request $request)
	{
		$n = Notification::find($request->get('id'));

		if ($n)
		{
			if ($n->to->id === Auth::user()->id)
			{
				$c = $n->read == true;

				if ($c)
				{
					$n->read = false;
				}
				else
				{
					$n->read = true;
				}

				if ($n->save())
				{
					return 'marked';
				}
				else
				{
					return 'fail';
				}
			}
		}
	}

}