<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestRentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user_bid' => ['required', 'numeric'],
            'apartment_id' => ['required'],
            'user_message' => ['required', 'min:2'],
            'accepted_by_user' => ['boolean'],
            'start' => ['date', 'required' , 'after:today'],
            'end' => ['date', 'required', 'after:start'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
