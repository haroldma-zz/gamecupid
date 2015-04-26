<?php namespace App\Http\Requests;

use Session;
use Illuminate\Foundation\Http\FormRequest;

class RegisterFormRequest extends FormRequest {

	public function rules()
	{
		return [
			'first_name' => 'required',
			'last_name'  => 'required',
			'username'   => 'required|unique:users',
			'email'      => 'required|email|unique:users',
			'password'   => 'required|min:4'
		];
	}

	public function messages()
	{
		return [
			'first_name.required' => 'You have to fill in your first name.',
			'last_name.required'  => 'You have to fill in your last name.',
			'username.required'   => 'Choose a username.',
			'username.unique'     => 'The username you picked is already in use.',
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
