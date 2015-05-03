<?php namespace App\Http\Requests;

use Session;
use Illuminate\Foundation\Http\FormRequest;

class LoginFormRequest extends FormRequest {

	public function rules()
	{
		return [
			'login_username' => 'required',
			'login_password' => 'required|min:4'
		];
	}

	public function messages()
	{
		return [
			'login_username.required' => 'You need an e-mail address to login.',
			'login_password.required' => 'You forgot to fill in a password.',
			'login_password.min'      => 'Your password must be at least 4 characters long.'
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
