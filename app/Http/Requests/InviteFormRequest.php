<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteFormRequest extends FormRequest {

	public function rules()
	{
		return [
			'player_count'         => 'required|numeric',
			'game_search'          => 'required',
			'game_id'              => 'required|integer',
			'console_id'           => 'required|integer|not_in:0',
			'title'                => 'required',
			'self_text'            => 'required',
			'requires_approval'    => 'boolean',
			'g-recaptcha-response' => 'required|recaptcha',
		];
	}

	public function messages()
	{
		return [
			'player_count.required'         => 'The player count field can\'t be empty.',
			'player_count.numeric'          => 'Only numeric values are allowed in the player count field.',
			'game_search.required'          => 'You forgot to choose a game.',
			'game_id.required'              => 'You forgot to choose a game.',
			'game_id.integer'               => 'Filthy hacker.',
			'console_id.required'           => 'Choose a console.',
			'console_id.integer'            => 'You can\'t do that.',
			'console_id.not_in'             => 'Choose a console.',
			'title.required'                => 'Come up with a title for your invite.',
			'self_text.required'            => 'Every invite on GameCupid needs a description.',
			'requires_approval.boolean'     => 'You can\'t do that.',
			'g-recaptcha-response.required' => 'You forgot to do fill out the captcha field.'
		];
	}

	public function authorize()
	{
		/**
		*
		* should check if user authenticated
		*
		**/

		return true;
	}

}