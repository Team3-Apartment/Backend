<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Rent;
use Auth;

class FeedbackController extends Controller
{
    public function store($apartment_id)
    {
        $validated = request()->validate([
            'rating' => ['required', 'numeric', 'between:1,5'],
        ]);
        $rent = Rent::where('user_id', Auth::id())
            ->where('apartment_id', $apartment_id)
//            ->where('status', 'accepted')
            ->firstOrFail();
        $rent->apartment->feedbacks()->updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'user_id' => Auth::id(),
                'rating' => $validated['rating'],
            ]);

        $apartment = Apartment::where('id', $apartment_id)->with('feedbacks')->first();
        return response()->json($apartment);
    }
}
