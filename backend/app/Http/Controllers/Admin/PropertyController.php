<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    // GET /api/admin/properties (all statuses)
    public function index(Request $request)
    {
        $query = Property::with(['location', 'type', 'primaryImage'])
            ->filter($request->only(['location_id', 'type_id', 'q']))
            ->latest();

        return PropertyResource::collection($query->paginate(15)->withQueryString());
    }

    // POST /api/admin/properties
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $property = Property::create([
            ...collect($data)->except('images')->all(),
            'admin_id' => $request->user()->id,
        ]);

        $this->saveImages($property, $request);

        return (new PropertyResource($property->load(['location', 'type', 'images', 'primaryImage'])))
            ->response()->setStatusCode(201);
    }

    // GET /api/admin/properties/{property}
    public function show(Property $property)
    {
        return new PropertyResource($property->load(['location', 'type', 'images', 'primaryImage']));
    }

    // PUT /api/admin/properties/{property}
    public function update(Request $request, Property $property)
    {
        $data = $request->validate($this->rules(partial: true));

        $property->update(collect($data)->except('images')->all());

        $this->saveImages($property, $request);

        return new PropertyResource($property->load(['location', 'type', 'images', 'primaryImage']));
    }

    // DELETE /api/admin/properties/{property}
    public function destroy(Property $property)
    {
        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $property->delete();

        return response()->noContent();
    }

    private function rules(bool $partial = false): array
    {
        $req = $partial ? 'sometimes' : 'required';

        return [
            'title' => [$req, 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => [$req, 'numeric', 'min:0'],
            'location_id' => [$req, 'exists:locations,id'],
            'type_id' => [$req, 'exists:property_types,id'],
            'bedrooms' => [$req, 'integer', 'min:0', 'max:20'],
            'bathrooms' => [$req, 'integer', 'min:0', 'max:20'],
            'floor_area' => [$req, 'numeric', 'min:1'],
            'status' => ['sometimes', 'in:Available,Unavailable'],
            'images' => ['sometimes', 'array', 'max:8'],
            'images.*' => ['image', 'max:4096'],
        ];
    }

    private function saveImages(Property $property, Request $request): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $hasPrimary = $property->images()->where('is_primary', true)->exists();

        foreach ($request->file('images') as $i => $file) {
            $property->images()->create([
                'image_path' => $file->store('properties', 'public'),
                'is_primary' => ! $hasPrimary && $i === 0,
            ]);
        }
    }
}
