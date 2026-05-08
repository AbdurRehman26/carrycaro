<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class TripController extends Controller
{
    /**
     * List all trips (with optional filters).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Trip::with(['fromCity.country', 'toCity.country', 'user']);

        // Filter by departure city
        if ($request->filled('from_city_id')) {
            $query->where('from_city_id', $request->from_city_id);
        }

        // Filter by arrival city
        if ($request->filled('to_city_id')) {
            $query->where('to_city_id', $request->to_city_id);
        }

        // Filter by departure date range
        if ($request->filled('departure_from')) {
            $query->whereDate('departure_date', '>=', $request->departure_from);
        }
        if ($request->filled('departure_to')) {
            $query->whereDate('departure_date', '<=', $request->departure_to);
        }

        // Only future/current trips by default
        if (! $request->boolean('include_past')) {
            $query->whereDate('departure_date', '>=', now()->toDateString());
        }

        $trips = $query->latest('departure_date')->paginate($request->integer('per_page', 15));

        return TripResource::collection($trips);
    }

    /**
     * List authenticated user's trips.
     */
    public function myTrips(Request $request): AnonymousResourceCollection
    {
        $trips = $request->user()
            ->trips()
            ->with(['fromCity.country', 'toCity.country'])
            ->latest('departure_date')
            ->paginate($request->integer('per_page', 15));

        return TripResource::collection($trips);
    }

    /**
     * Store a new trip.
     */
    public function store(StoreTripRequest $request): JsonResponse
    {
        $trip = $request->user()->trips()->create($request->validated());

        $trip->load(['fromCity.country', 'toCity.country', 'user']);

        return response()->json([
            'message' => 'Trip created successfully.',
            'trip' => new TripResource($trip),
        ], 201);
    }

    /**
     * Show a single trip.
     */
    public function show(Trip $trip): JsonResponse
    {
        $trip->load(['fromCity.country', 'toCity.country', 'user']);

        return response()->json([
            'trip' => new TripResource($trip),
        ]);
    }

    /**
     * Update an existing trip.
     */
    public function update(UpdateTripRequest $request, Trip $trip): JsonResponse
    {
        // Ensure the user owns this trip
        if ($trip->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $trip->update($request->validated());
        $trip->load(['fromCity.country', 'toCity.country', 'user']);

        return response()->json([
            'message' => 'Trip updated successfully.',
            'trip' => new TripResource($trip),
        ]);
    }

    /**
     * Delete a trip.
     */
    public function destroy(Request $request, Trip $trip): JsonResponse
    {
        if ($trip->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $trip->delete();

        return response()->json([
            'message' => 'Trip deleted successfully.',
        ]);
    }
}
