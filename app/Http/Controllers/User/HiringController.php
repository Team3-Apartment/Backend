<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestRentRequest;
use App\Http\Requests\UpdateRentRequest;
use App\Models\Apartment;
use App\Models\Feedback;
use App\Models\Rent;
use Auth;
use Carbon\Carbon;
use Exception;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class HiringController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::where('user_id', Auth::id())->get();
        return response()->json([
            'data' => Auth::user()
                ->rents()
                ->with(['apartment.user'])
                ->get()
                ->map(function ($item) use ($feedbacks) {
                    $item->feedback = optional($feedbacks->where('apartment_id', $item->apartment_id)->first())->rating;
                    return $item;
                }),
        ]);
    }

    public function requestRent(RequestRentRequest $request)
    {
        $apartment = Apartment::findOrFail($request->input('apartment_id'));
        $hire = Auth::user()->rents()->updateOrCreate(
            [
                'apartment_id' => $apartment->id,
                'status' => 'negotiate',
            ]
            , [
            'apartment_id' => $apartment->id,
            'user_bid' => $request->user_bid,
            'user_message' => $request->input('user_message'),
            'accepted_by_user' => $request->input('accepted_by_user') ?? true,
            'status' => 'negotiate',
            'start' => Carbon::parse($request->input('start'))->toDateString(),
            'end' => Carbon::parse($request->input('end'))->toDateString(),
        ]);

        return response()->json($hire);
    }

    public function accept(Rent $rent)
    {
        abort_if($rent->user_id != Auth::id(), 404, 'can only modify your hires');
        abort_if($rent->status != 'negotiate', 400, "can't accept the hire");

        $rent->accepted_by_user = true;
        if ($rent->accepted_by_provider) {
            $rent->status = 'accepted';
        }
        $rent->user_bid = $rent->provider_bid;
        $rent->save();
        return response()->json($rent);
    }

    public function pay(Rent $rent)
    {
        abort_if($rent->user_id != Auth::id(), 404, 'can only pay your hires');
        abort_if($rent->status != 'accepted', 400, "can only pay for accepted hires");
        Stripe::setApiKey('sk_test_51IeuxWJ7sndJ8uAFgdYTOYBNaJ9Us3NKcby1lSCUvPhIF59Au7rG1gn0AS7e6sHSXKw9nZHqBfeE1p4lr5ftNCBW00XIuP3YMl');
        $clientSecret = null;

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) $rent->user_bid ,
                'currency' => 'usd',
            ]);

            $clientSecret = $paymentIntent->client_secret;
        } catch (Exception $e) {
            abort(500, $e->getMessage());
        }
        $rent->paid = true;
        $rent->save();
        return response()->json([
            'message' => 'payment created successfully',
            'client_secret' => $clientSecret,
        ]);
    }

    public function cancel(Rent $rent)
    {
        abort_if($rent->user_id != Auth::id(), 404, 'can only modify your hires');
        abort_if($rent->status != 'negotiate', 400, "can't cancel the hire");
        $rent->status = 'canceled';
        $rent->save();
        return response()->json($rent);
    }

}
