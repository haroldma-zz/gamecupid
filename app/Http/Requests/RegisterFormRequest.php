<?php namespace App\Http\Requests;

use Session;
use Illuminate\Foundation\Http\FormRequest;

class RegisterFormRequest extends FormRequest {

	public function rules()
	{
		return [
			'username'   => 'required|unique:users',
			'timezone'   => 'required',
			'email'      => 'required|email|unique:users',
			'password'   => 'required|min:4'
		];
	}

	public function messages()
	{
		return [
			'username.required'   => 'Choose a username.',
			'username.unique'     => 'The username you picked is already in use.',
			'timezone.required'   => 'Set your timezone.',
			'email.required'      => 'You need an e-mail address to register.',
			'email.email'         => 'The e-mail address you provided is not a valid e-mail.',
			'email.unique'        => 'The e-mail address you provided is already in use, try another one.',
			'password.required'   => 'You forgot to fill in a password.',
			'password.min'        => 'Your password must be at least 4 characters long.'
		];
	}

	public function authorize()
	{
		return true;
	}

	public function response(array $errors)
	{
		Session::flash('errors-for', 'register');
		return redirect()->back()->withInput()->withErrors($errors);
	}

}
