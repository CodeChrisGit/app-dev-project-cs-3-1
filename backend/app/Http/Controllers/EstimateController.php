<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EstimateController extends Controller
{
    // POST /api/estimate
    // bedrooms, bathrooms, floor_area, land_area

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'bedrooms' => ['required', 'integer', 'min:1', 'max:20'],
            'bathrooms' => ['required', 'integer', 'min:1', 'max:20'],
            'floor_area' => ['required', 'numeric', 'min:10', 'max:2000'],
            'land_area' => ['required', 'numeric', 'min:0', 'max:10000'],
        ]);

        try {
            $response = Http::timeout(10)->post(
                rtrim(config('services.ml.url'), '/') . '/predict',
                $data
            );
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'The estimator service is not running.'
            ], 503);
        }

        if ($response->failed()) {
            return response()->json([
                'message' => 'The estimator could not produce a price.'
            ], 502);
        }

        return response()->json([
            'estimated_price' => (float) $response->json('estimated_price')
        ]);
    }
}