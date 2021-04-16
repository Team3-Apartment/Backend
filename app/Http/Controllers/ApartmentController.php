<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Service;
use App\Services\UploadImage;
use Auth;
use DB;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{

    public function index()
    {
        $apartments = Apartment::with('user:id,name')
            ->withCount('feedbacks')
            ->withCount([
                'feedbacks as feedbacks_avg' => fn($q) => $q->select(DB::raw('avg(rating) as avg_rating')),
            ])
            ->paginate();

        return response()->json($apartments);
    }

    public function myApartment()
    {
        $apartments = Apartment::where('user_id', Auth::id())->with('user:id,name')->get();

        return response()->json($apartments);
    }

    public function store(Request $request)
    {
        $request->validate(['price' => 'required']);
        $apartment = Apartment::create([
            'description' => $request->description,
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'price' => $request->price,
        ]);

        return response()->json($apartment);
    }

    public function show(Apartment $apartment)
    {
        $apartment = Apartment::where('id' , $apartment->id)
            ->withCount('feedbacks')
            ->withCount([
                'feedbacks as feedbacks_avg' => fn($q) => $q->select(DB::raw('avg(rating) as avg_rating')),
            ])
            ->first();
        return response()->json($apartment);
    }

    public function update(Request $request, Apartment $apartment)
    {
        abort_if($apartment->user_id != Auth::id(), 404, 'you can only modify your apartments');
        $apartment->update($request->all());
        return response()->json(
            $apartment
        );
    }

    public function destroy(Apartment $apartment)
    {
        abort_if($apartment->user_id != Auth::id(), 404, 'you can only delete your apartments');
        $apartment->delete();
        return response()->json([]);
    }

    public function addImage(Request $request, Apartment $apartment, UploadImage $uploadImage)
    {
        $request->validate([
            'images' => ['array', 'required'],
            'images.*' => ['required', 'image', 'max:10000'],
        ]);
        abort_if($apartment->user_id != Auth::user()->id, 400, 'you can only edit your services');
        $images = [];
        foreach ($request->images as $image) {
            $images [] = $uploadImage->uploadBase64($image, env('AWS_BUCKET'), 'apartments');
        }

        $apartment->update(['images' => $images]);

        return response()->json(
            $apartment
        );
    }
}
