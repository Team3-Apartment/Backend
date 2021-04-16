<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RespondToRentRequest extends FormRequest
{
	public function rules()
	{
		return [
            'provider_bid' => ['nullable', 'min:0' , 'numeric'],
            'provider_message' => ['nullable'],
            'accepted_by_provider' => ['nullable' , 'boolean'],
		];
	}

	public function authorize()
	{
		return true;
	}
}
