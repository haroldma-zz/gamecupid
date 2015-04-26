<?php namespace App\Http\Requests;

use Session;
use Illuminate\Foundation\Http\FormRequest;

class ConnectFormRequest extends FormRequest {

	public function rules()
	{
		return [
			'email'      => 'required|email',
			'password'   => 'required'
		];
	}

	public function messages()
	{
		return [
			'email.required'      => 'You forgot to fill in an e-mail.',
			'email.email'         => 'The e-mail address you provided is not a valid e-mail.',
			'password.required'   => 'You forgot to fill in a password.'
		];
	}

	public function authorize()
	{
		return true;
	}

	public function response(array $errors)
	{
		return redirect()->back()->withInput()->withErrors($errors);
	}

}
