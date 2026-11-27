<?php

namespace App\Http\Controllers;

use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    // GET /api/properties?location_id=&type_id=&min_price=&max_price=&bedrooms=&q=&sort=
    public function index(Request $request)
    {
        $query = Property::with(['location', 'type', 'primaryImage'])
            ->where('status', 'Available')
            ->filter($request->only(['location_id', 'type_id', 'min_price', 'max_price', 'bedrooms', 'q']));

        match ($request->query('sort')) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        return PropertyResource::collection($query->paginate(12)->withQueryString());
    }

    // GET /api/properties/{property}
    public function show(Property $property)
    {
        return new PropertyResource($property->load(['location', 'type', 'images', 'primaryImage']));
    }

    // GET /api/properties/compare?ids=1,2,3
    public function compare(Request $request)
    {
        $ids = collect(explode(',', (string) $request->query('ids')))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->take(3);

        $properties = Property::with(['location', 'type', 'primaryImage'])
            ->whereIn('id', $ids)
            ->get();

        return PropertyResource::collection($properties);
    }
}
