<?php namespace App\Http\Requests;

use Auth;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class InviteFormRequest extends FormRequest {

	public function rules()
	{
		return [
			'player_count' => 'required|numeric',
			'game_id'      => 'required',
			'console_id'   => 'required',
			'title'        => 'required',
			'self_text'    => 'required',
		];
	}

	public function messages()
	{
		return [
			'player_count.required' => 'The player count field can\'t be empty.',
			'player_count.numeric'  => 'Only numeric values are allowed in the player count field.',
			'game_id.required'      => 'You forgot to choose a game.',
			'console_id.required'   => 'Choose a console.',
			'title.required'        => 'Come up with a title for your invite.',
			'self_text.required'    => 'Every invite on GameCupid needs a description.'
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