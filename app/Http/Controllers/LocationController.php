<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\LocationImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function search(Request $request)
    {
        $search = $request->input('query');
        $locations = Location::where('title', 'like', "%$search%")->paginate(15);
        if ($locations->isEmpty()) {
            return response()->json([
                'message' => 'No locations found',
                'success' => false
            ], 404);
        }
        return response()->json([
            'locations' => LocationResource::collection($locations),
            'pagination' => [
                'total' => $locations->total(),
                'per_page' => $locations->perPage(),
                'current_page' => $locations->currentPage(),
                'last_page' => $locations->lastPage(),
                'next_page_url' => $locations->nextPageUrl(),
                'prev_page_url' => $locations->previousPageUrl(),
                'from' => $locations->firstItem(),
                'to' => $locations->lastItem(),
                'path' => $locations->path(),
                'links' => $locations->toArray()['links'] ?? [],
            ],
            'message' => 'Locations fetched successfully'
        ], 200);
    }

    public function index()
    {
        $locations = Location::paginate(15);

        if ($locations->isEmpty()) {
            return response()->json([
                'message' => 'Locations not found',
                'success' => false
            ], 404);
        }

        return response()->json([
            'locations' => LocationResource::collection($locations),
            'pagination' => [
                'total' => $locations->total(),
                'per_page' => $locations->perPage(),
                'current_page' => $locations->currentPage(),
                'last_page' => $locations->lastPage(),
                'next_page_url' => $locations->nextPageUrl(),
                'prev_page_url' => $locations->previousPageUrl(),
                'from' => $locations->firstItem(),
                'to' => $locations->lastItem(),
                'path' => $locations->path(),
                'links' => $locations->toArray()['links'] ?? [],
            ],
            'message' => 'Locations fetched successfully',
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request)
    {
        try {
            $validated = $request->validated();

            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['title']);
            } else {
                $validated['slug'] = Str::slug($validated['slug']);
            }

            $location = Location::create($validated);

            if ($request->hasFile('location_image')) {
                $image = $request->file('location_image');
                $newImageName = 'location-' . time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('location_images/', $newImageName, 'public');
                LocationImage::create([
                    'location_id' => $location->id,
                    'image_path' => $newImageName
                ]);
            }
            return response()->json([
                'location' => new LocationResource($location),
                'message' => 'Location created successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Location not created',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $location = new LocationResource($location);

        return response()->json([
            'location' => $location,
            'message' => 'Location fetched successfully'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateImage(Request $request, Location $location)
    {
        $validated = $request->validate([
            'location_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $oldImage = LocationImage::where('location_id', $location->id)->first();

        if ($oldImage) {
            Storage::disk('public')->delete('location_images/' . $oldImage->image_path);
            $oldImage->delete();
        }

        if ($request->hasFile('location_image')) {
            $image = $validated['location_image'];
            $newImage = 'location-' . time() . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('location_images/', $newImage, 'public');
            LocationImage::create([
                'location_id' => $location->id,
                'image_path' => $newImage
            ]);
        }

        return response()->json([
            'message' => 'Location images updated successfully.',
        ], 200);
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        try {
            $validatedData = $request->validated();

            if (empty($validatedData['slug'])) {
                $validatedData['slug'] = Str::slug($validatedData['title']);
            } else {
                $validatedData['slug'] = Str::slug($validatedData['slug']);
            }

            $location->update($validatedData);

            return response()->json([
                'location' => new LocationResource($location),
                'message' => 'Location updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Location not updated',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {

        $oldImage = LocationImage::where('location_id', $location->id)->first();

        if ($oldImage) {
            Storage::disk('public')->delete('location_images/' . $oldImage->image_path);
            $oldImage->delete();
        }
        $location->delete();
        return response()->json([
            'message' => 'Location deleted successfully'
        ], 200);
    }
}
