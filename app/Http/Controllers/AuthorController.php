<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $searchTerm = $request->input('q');
        $validSortColumns = ['name',"position","detail"];
        $sortBy = in_array($request->input('sort_by'), $validSortColumns, true) ? $request->input('sort_by') : 'id';
        $sortDirection = in_array($request->input('sort_direction'), ['asc', 'desc'], true) ? $request->input('sort_direction') : 'desc';
        $limit = $request->input('limit', 5);

        $limit = is_numeric($limit) && $limit > 0 && $limit <= 100 ? (int)$limit : 5;

        $query = Author::query();

        if ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');

        }

        $query->orderBy($sortBy, $sortDirection);

        $author = $query->paginate($limit);

        $author->appends([
            'q' => $searchTerm,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'limit' => $limit,
        ]);

        return AuthorResource::collection($author);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request)
    {
        //
        $author = new Author();
        $image = $request->file('author_image');
        $imageName = 'Author_image_' . uniqid() . '.' . $image->extension();
        $image->storeAs("images/author_image", $imageName, "public");

        $author->author_image = asset('storage/' . $imageName);

        $author->name = $request->name;
        $author->position = $request->position;
        $author->detail = $request->detail;
        $author->facebook = $request->facebook;
        $author->tiktok = $request->tiktok;
        $author->youtube = $request->youtube;
        $author->save();

        return response()->json([
            'data' => new AuthorResource($author),
            'message' => "Author created successfully"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        //

        $author = Author::findOrFail($author->id);

        return response()->json([
            'data' => new AuthorResource($author),
            'message' => "Author retrieved  successfully"

        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, Author $author)
    {
        //

        $author->update($request->only([
            'name',
            'position',
            'detail',
            'facebook',
            'tiktok',
            'youtube'
        ]));

        return response()->json([
            'message' => 'Author updated successfully',
            'data' => new AuthorResource($author)
        ],201);

    }

    public function updateAuthorImage(Request $request, $id)
    {
        // Validate the new image
        $request->validate([
            'author_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the author record
        $author = Author::findOrFail($id);

        if (!$author) {
            return response()->json(['message' => 'Author not found'], 404);
        }

        // Check if there's an existing image
        if ($author->author_image) {
            $oldImagePath = str_replace(asset('storage/'), 'images/author_image', $author->author_image);

            // Delete the old image if it exists
            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        // Store the new image
        $image = $request->file('author_image');
        $imageName = 'Author_image_' . uniqid() . '.' . $image->extension();
        $image->storeAs("images/author_image", $imageName, "public");

        // Update the author image path in the database
        $author->author_image = asset('storage/' . $imageName);
        $author->save();

        return response()->json([
            'message' => 'Author image updated successfully!',
            'data' => new AuthorResource($author)
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        //
        $id = $author->id;
        $validated = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:authors,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Invalid menu ID'
            ], 404);
        }

        $author = Author::findOrFail($author->id);

        if ($author->author_image) {
            $oldImagePath = str_replace(asset('storage/'), 'images/author_image', $author->author_image);

            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        $author->delete();

        return response()->json([
            'message' => 'Author deleted successfully'
        ]);

    }
}
