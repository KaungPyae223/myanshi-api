<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;
use App\Http\Resources\GalleryResource;
use App\Models\Gallery;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ClientRequest $request)
    {
        //
        $searchTerm = $request->input('q');
        $validSortColumns = ['tik_tok_link'];
        $sortBy = in_array($request->input('sort_by'), $validSortColumns, true) ? $request->input('sort_by') : 'id';
        $sortDirection = in_array($request->input('sort_direction'), ['asc', 'desc'], true) ? $request->input('sort_direction') : 'desc';
        $limit = $request->input('limit', 5);

        $limit = is_numeric($limit) && $limit > 0 && $limit <= 100 ? (int)$limit : 5;

        $query = Gallery::query();

        if ($searchTerm) {
            $query->where('tik_tok_link', 'like', '%' . $searchTerm . '%');
        }

        $query->orderBy($sortBy, $sortDirection);

        $gallery = $query->paginate($limit);

        $gallery->appends([
            'q' => $searchTerm,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'limit' => $limit,
        ]);

        return GalleryResource::collection($gallery);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGalleryRequest $request)
    {
        //

        $gallery = new Gallery();

        $image = $request->file('image');
        $imageName = 'Gallery_image_' . uniqid() . '.' . $image->extension();
        $image->storeAs("images/gallery_image/", $imageName, "public");

        $gallery->image = asset('storage/' . $imageName);

        $gallery->tik_tok_link = $request->tik_tok_link;
        $gallery->save();

        return response()->json([
            'message' => 'Gallery created successfully',
            'data' => new GalleryResource($gallery)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Gallery $gallery)
    {
        //
        $gallery = Gallery::findOrFail($gallery->id);

        return response()->json([
            'data' => new GalleryResource($gallery),
            'message' => 'Gallery retrieved successfully'
        ]);
    }





    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGalleryRequest $request, Gallery $gallery)
    {
        //
        $gallery->update($request->only([
            'tik_tok_link'
        ]));

        return response()->json([
            'message' => 'Gallery updated successfully',
            'data' => new GalleryResource($gallery)
        ], 201);
    }

    public function updateGalleryImage(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $gallery = Gallery::findOrFail($id);

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        if ($gallery->image) {
            $oldImagePath = str_replace(asset('storage/'), 'images/gallery_image', $gallery->image);

            // Delete the old image if it exists
            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        $image = $request->file('image');


        $imageName = 'Gallery_image_' . uniqid() . '.' . $image->extension();
        $imagePath = $image->storeAs('images/gallery_image', $imageName, 'public');

        $gallery->image = asset('storage/' . $imagePath);
        $gallery->save();




        // Return a success response
        return response()->json([
            'message' => 'Gallery image updated successfully!',
            'data' => new GalleryResource($gallery)
        ], 201);


        
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery)
    {
        //

        $id = $gallery->id;

        $validated = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:galleries,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Invalid menu ID'
            ], 404);
        }

        $gallery->findOrFail($gallery->id);
        // Delete image if exists
        if ($gallery->image) {
            $oldImagePath = str_replace(asset('storage/'), 'images/gallery_image', $gallery->image);

            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }


        $gallery->delete();

        return response()->json([
            'message' => 'Gallery deleted successfully'
        ]);
    }
}
