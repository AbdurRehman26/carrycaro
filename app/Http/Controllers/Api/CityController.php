<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CountryResource;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CityController extends Controller
{
    /**
     * Search cities by name.
     */
    public function index(Request $request): JsonResponse
    {
        $query = City::with('country');
        $search = $request->input('search', $request->input('q'));

        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $cities = $query->limit(50)->get();

        return response()->json([
            'cities' => CityResource::collection($cities),
        ]);
    }

    /**
     * List all countries.
     */
    public function countries(Request $request): JsonResponse
    {
        $query = Country::query()->orderBy('name');

        if ($requestSearch = $request->input('search', $request->input('q'))) {
            $query->where('name', 'like', '%'.$requestSearch.'%');
        }

        $countries = $query->get();

        return response()->json([
            'countries' => CountryResource::collection($countries),
        ]);
    }
}
