<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\RespondToRentRequest;
use App\Models\Rent;
use Auth;

class HiringController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return response()->json([
            'data' => $user->apartments()->with('rents')->get(),
        ]);
    }

    public function show(Rent $rent)
    {
        abort_if($rent->apartment->user_id != Auth::id(), 404);
        return response()->json($rent);
    }

    public function respondToRent(RespondToRentRequest $request, Rent $rent)
    {
        abort_if($rent->apartment->user_id != Auth::id(), 404);

        $rent->update($request->validated());
        $rent->accepted_by_provider = true;
        $rent->accepted_by_user = false;
        $rent->save();
        return response()->json($rent);
    }

    public function markCompleted(Rent $rent)
    {
        abort_if($rent->apartment->user_id != Auth::id(), 404);

        abort_if($rent->status != 'accepted', 400, "can't complete non accepted hires");

        $rent->completed = true;
        $rent->status = 'completed';
        $rent->save();
        return response()->json($rent);
    }

    public function accept(Rent $rent)
    {
        abort_if($rent->apartment->user_id != Auth::id(), 404);

        abort_if($rent->status != 'negotiate', 400, "can't cancel the hire");

        $rent->accepted_by_provider = true;
        $rent->status = 'accepted';
        $rent->provider_bid = $rent->user_bid;
        $rent->save();
        return response()->json($rent);
    }

    public function cancel(Rent $rent)
    {
        abort_if($rent->apartment->user_id != Auth::id(), 404);

        abort_if($rent->status != 'negotiate', 400, "can't cancel the hire");
        $rent->status = 'canceled';
        $rent->save();
        return response()->json($rent);
    }
}
