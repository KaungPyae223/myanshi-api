<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\CustomerImage;
use App\Models\ReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function search(Request $request)
    {
        $search = $request->input('query');
        $reviews = Review::where('customer_name', 'like', "%$search%")
            ->orWhere('review_title', 'like', "%$search%")
            ->paginate(15);

        if ($reviews->isEmpty()) {
            return response()->json([
                'message' => 'No reviews found'
            ], 404);
        }
        return response()->json([
            'reviews' => ReviewResource::collection($reviews),
            'pagination' => [
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'next_page_url' => $reviews->nextPageUrl(),
                'prev_page_url' => $reviews->previousPageUrl(),
                'from' => $reviews->firstItem(),
                'to' => $reviews->lastItem(),
                'path' => $reviews->path(),
                'links' => $reviews->toArray()['links'] ?? [],
            ],
            'message' => 'Review fetched successfully'
        ], 200);
    }

    public function index()
    {
        $reviews = Review::paginate(15);

        if ($reviews->isEmpty()) {
            return response()->json([
                'message' => 'No reviews found',
                'success' => false
            ], 404);
        }

        return response()->json([
            'reviews' => ReviewResource::collection($reviews),
            'pagination' => [
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'next_page_url' => $reviews->nextPageUrl(),
                'prev_page_url' => $reviews->previousPageUrl(),
                'from' => $reviews->firstItem(),
                'to' => $reviews->lastItem(),
                'path' => $reviews->path(),
                'links' => $reviews->toArray()['links'] ?? [],
            ],
            'message' => 'Review fetched successfully'
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
    public function store(StoreReviewRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $review = Review::create($validatedData);

            if ($request->hasFile('customer_image')) {
                $image = $request->file('customer_image');
                $newImage = 'customer-' . time() . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('customer_images/', $newImage, 'public');
                CustomerImage::create([
                    'review_id' => $review->id,
                    'image_path' => $newImage
                ]);
            }

            if ($request->hasFile('review_image')) {
                $image = $request->file('review_image');
                $newImage = 'review-' . time() . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('review_images/', $newImage, 'public');
                ReviewImage::create([
                    'review_id' => $review->id,
                    'image_path' => $newImage
                ]);
            }

            return response()->json([
                'review' => new ReviewResource($review),
                'message' => 'Review created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Review not created',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return response()->json([
            'review' => new ReviewResource($review),
            'message' => 'Review fetched successfully'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        try {
            $validated = $request->validated();
            $review->update($validated);
            return response()->json([
                'review' => new ReviewResource($review),
                'message' => 'Review updated successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Review not updated',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateReviewImage(Request $request, Review $review)
    {
        $validated = $request->validate([
            'review_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $oldImage = ReviewImage::where('review_id', $review->id)->first();

        if ($oldImage) {
            Storage::disk('public')->delete('review_images/' . $oldImage->image_path);
            $oldImage->delete();
        }

        if ($request->hasFile('review_image')) {
            $image = $validated['review_image'];
            $newImage = 'review-' . time() . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('review_images/', $newImage, 'public');
            ReviewImage::create([
                'review_id' => $review->id,
                'image_path' => $newImage
            ]);
        }

        return response()->json([
            'message' => 'Review image updated successfully.'
        ], 200);
    }

    public function updateCustomerImage(Request $request, Review $review)
    {
        $validated = $request->validate([
            'customer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $oldImage = CustomerImage::where('review_id', $review->id)->first();

        if ($oldImage) {
            Storage::disk('public')->delete('customer_images/' . $oldImage->image_path);
            $oldImage->delete();
        }

        if ($request->hasFile('customer_image')) {
            $image = $validated['customer_image'];
            $newImage = 'customer-' . time() . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('customer_images/', $newImage, 'public');
            CustomerImage::create([
                'review_id' => $review->id,
                'image_path' => $newImage
            ]);
        }

        return response()->json([
            'message' => 'Customer image updated successfully.'
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        if ($review->reviewImage) {
            Storage::disk('public')->delete('review_images/' . $review->reviewImage->image_path);
            $review->reviewImage->delete();
        }
        if ($review->customerImage) {
            Storage::disk('public')->delete('customer_images/' . $review->customerImage->image_path);
            $review->customerImage->delete();
        }

        $review->delete();
        return response()->json([
            'message' => 'Review deleted successfully'
        ], 200);
    }
}
