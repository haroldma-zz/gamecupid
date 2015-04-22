<?php namespace App\Http\Requests;

use Session;
use Illuminate\Foundation\Http\FormRequest;

class LoginFormRequest extends FormRequest {

	public function rules()
	{
		return [
			'email'      => 'required|email',
			'password'   => 'required|min:4'
		];
	}

	public function messages()
	{
		return [
			'email.required'      => 'You need an e-mail address to login.',
			'email.email'         => 'The e-mail address you provided is not a valid e-mail.',
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
		Session::flash('errors-for', 'login');
		return redirect()->back()->withInput()->withErrors($errors);
	}

}
