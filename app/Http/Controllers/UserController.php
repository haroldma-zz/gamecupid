<?php namespace App\Http\Controllers;

use Hash;
use Auth;
use App\Enums\RepEvents;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use Illuminate\Database\Eloquent\Collection;

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
			// Give the user rep
            giveRepAndNotified(RepEvents::REGISTERED, $user->id); # User not logged in yet, need to pass $user->id

			// Confirm e-mail notification
			/*$notification              = new Notification;
            $not->type    = NotificationTypes::TEXT;
			$notification->title       = "Confirm your e-mail address.";
			$notification->description = "By confirming your e-mail address, you become a verified user. You will also earn some more Rep which brings you closer to being a GameCupid legend.";
			$notification->to_id       = $user->id;
			$notification->save();*/

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

		$start = time();
		$end   = $start + 30;

		$n             = [];
		$notifications = Auth::user()->rNotifications()->where('notified', false)->orderBy('id', 'DESC')->get();
		$check         = count($notifications) > 0;

		while ($check === false && $start < $end) {
			sleep(5);

            $notifications     = Auth::user()->rNotifications()->where('notified', false)->orderBy('id', 'DESC')->get();
            $check = count($notifications) > 0;
			$start = time();
		}

		if ($check)
		{
            foreach ($notifications as $not)
            {
                $not->notified = true;
                $not->save();

                // create the dto
                $n[] = $not->createDto();
            }
		}

		return response()->json(new Collection($n));
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