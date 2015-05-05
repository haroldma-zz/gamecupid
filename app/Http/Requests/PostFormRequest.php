<?php namespace App\Http\Requests;

use Auth;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class PostFormRequest extends FormRequest {

	public function rules()
	{
		return [
			'max_players' => 'required|numeric',
			'game_id'     => 'required',
			'console_id'  => 'required',
			'title'       => 'required',
			'self_text'   => 'required',
		];
	}

	public function messages()
	{
		return [
			'max_players.required' => 'The player count field can\'t be empty.',
			'max_players.numeric'  => 'Only numeric values are allowed in the player count field.',
			'game_id.required'     => 'You forgot to choose a game.',
			'console_id.required'  => 'Choose a console.',
			'title.required'       => 'Come up with a title for this post.',
			'self_text.required'   => 'Your post needs a description.'
		];
	}

	public function authorize()
	{
		/**
		*
		* should check if user authenticated
		*
		**/

		if (Auth::check())
			return true;

		return false;
	}

}