<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentRequest extends FormRequest
{
	public function rules()
	{
		return [
            'user_bid'=> ['nullable' , 'numeric' , 'min:0'],
            'apartment_id' => ['required'],
            'user_message' => ['nullable' , 'min:2']
		];
	}

	public function authorize()
	{
		return true;
	}
}
