<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('q');
        $validSortColumns = ['category'];
        $sortBy = in_array($request->input('sort_by'), $validSortColumns, true) ? $request->input('sort_by') : 'id';
        $sortDirection = in_array($request->input('sort_direction'), ['asc', 'desc'], true) ? $request->input('sort_direction') : 'desc';
        $limit = $request->input('limit', 5);

        $limit = is_numeric($limit) && $limit > 0 && $limit <= 100 ? (int)$limit : 5;

        $query = Category::query();


        if ($searchTerm) {
                $query->where('category', 'like', '%' . $searchTerm . '%');
        }

        $query->orderBy($sortBy, $sortDirection);

        $categories = $query->paginate($limit);

        $categories->appends([
            'q' => $searchTerm,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'limit' => $limit,
        ]);

        return CategoryResource::collection($categories);
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
    public function store(StoreCategoryRequest $request)
    {
        $category = new Category();

        $category->category = $request->category;
        $category->save();

        return response()->json([
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category)
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {

        $category->category = $request->category;
        $category->update();

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category)
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $validated = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:categories,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Invalid category ID'
            ], 404);
        }

        $product = Category::find($id);

        $product->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);

    }
}
