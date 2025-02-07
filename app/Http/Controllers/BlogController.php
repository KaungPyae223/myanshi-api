<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $searchTerm = $request->input('q');
        $validSortColumns = ['short_description',"content"];
        $sortBy = in_array($request->input('sort_by'), $validSortColumns, true) ? $request->input('sort_by') : 'id';
        $sortDirection = in_array($request->input('sort_direction'), ['asc', 'desc'], true) ? $request->input('sort_direction') : 'desc';
        $limit = $request->input('limit', 5);

        $limit = is_numeric($limit) && $limit > 0 && $limit <= 100 ? (int)$limit : 5;

        $query = Blog::query();

        if ($searchTerm) {
                $query->where('short_description', 'like', '%' . $searchTerm . '%');
        }

        $query->orderBy($sortBy, $sortDirection);

        $blog = $query->paginate($limit);

        $blog->appends([
            'q' => $searchTerm,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'limit' => $limit,
        ]);

        return BlogResource::collection($blog);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request)
    {
        //
        $blog = new Blog();

        $image = $request->file("blog_image");

        $imageName = "blog" . uniqid() . '.' . $image->extension();
        $imagePath = $image->storeAs("images/blogImage/", $imageName,"public");
        $image =  asset('storage/' . $imagePath);
        $blog->blog_image = $image;

        $blog->short_description = $request->short_description;
        $blog->content = $request->content;
        $blog->author_id = $request->author_id;
        $blog->save();
        return response()->json([
            'message' => 'Blog created successfully',
            'blog' => new BlogResource($blog)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        //
        $blog = Blog::findorFail($blog->id);

        return response()->json([
            'data' => new BlogResource($blog),
            'message' => 'Blog retrieved successfully'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        //

        $blog->update($request->only([
            'short_description',
            'content',
            'author_id'
        ]));

        return response()->json([
            'message' => 'Blog updated successfully',
            'blog' => new BlogResource($blog)
        ],201);
    }


    public function updatBlogImage(Request $request, $id)
    {
        // Validate the new image
        $request->validate([
            'blog_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the blog record
        $blog = Blog::findOrFail($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        // Check if there's an existing image
        if ($blog->blog_image) {
            $oldImagePath = str_replace(asset('storage/'), 'images/blogImage/', $blog->blog_image);

            // Delete the old image if it exists
            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        // Store the new image
        $image = $request->file('blog_image');
        $imageName = 'Blog_image_' . uniqid() . '.' . $image->extension();
        $image->storeAs("images/blogImage/", $imageName, "public");

        // Update the blog image path in the database
        $blog->blog_image = asset('storage/' . $imageName);
        $blog->save();

        return response()->json([
            'message' => 'Blog image updated successfully!',
            'data' => new BlogResource($blog)
        ], 201);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {

        //
        $id = $blog->id;

        $validated = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:blogs,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Invalid blog ID'
            ], 404);
        }

        $blog = Blog::findOrFail($id);

        // Delete blog image if exists
        if ($blog->blog_image) {
            $oldImagePath = str_replace(asset('storage/'), 'blogs/blogImage/', $blog->blog_image);

            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        $blog->delete();

        return response()->json([
            'message' => 'Blog deleted successfully'
        ]);
    }
}
