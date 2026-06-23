<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AirlineResource;
use App\Models\Airline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AirlineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Airline::query()
            ->where('is_active', true)
            ->orderBy('name');

        if ($search = $request->input('search', $request->input('q'))) {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('iata_code', 'like', '%'.$search.'%')
                    ->orWhere('icao_code', 'like', '%'.$search.'%')
                    ->orWhere('country', 'like', '%'.$search.'%');
            });
        }

        return response()->json([
            'airlines' => AirlineResource::collection($query->limit(100)->get()),
        ]);
    }
}
