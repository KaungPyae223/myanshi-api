<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFaqRequest;
use App\Http\Requests\UpdateFaqRequest;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //

        $searchTerm = $request->input('q');
        $validSortColumns = ['question', 'answer'];
        $sortBy = in_array($request->input('sort_by'), $validSortColumns, true) ? $request->input('sort_by') : 'id';
        $sortDirection = in_array($request->input('sort_direction'), ['asc', 'desc'], true) ? $request->input('sort_direction') : 'desc';
        $limit = $request->input('limit', 5);

        $limit = is_numeric($limit) && $limit > 0 && $limit <= 100 ? (int)$limit : 5;

        $query = Faq::query();


        if ($searchTerm) {
                $query->where('question', 'like', '%' . $searchTerm . '%')
                    ->orWhere('answer', 'like', '%' . $searchTerm . '%');
        }

        $query->orderBy($sortBy, $sortDirection);

        $faqs = $query->paginate($limit);

        $faqs->appends([
            'q' => $searchTerm,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'limit' => $limit,
        ]);

        return FaqResource::collection($faqs);


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFaqRequest $request)
    {
        //
        $faq = new Faq();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();

        return response()->json([
            'message' => 'Faq created successfully',
            'data'=> new FaqResource($faq)
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Faq $faq)
    {
        //
        $faq = Faq::findOrFail($faq->id);
        return response()->json([
            'data' => new FaqResource($faq),
            'message'=> 'Faq retrieved successfully'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFaqRequest $request, Faq $faq)
    {
        //
        $faq->update($request->only(['question','answer']));
        return response()->json([
            'message' => 'Faq updated successfully',
            'data' => new FaqResource($faq)
        ],201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        //
        $id = $faq->id;
        $validated = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:faqs,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Invalid Faq ID'
            ], 404);
        }


        $faq =  Faq::findOrFail($faq->id);
        $faq->delete();

        return response()->json([
            'message' => 'Faq deleted successfully'
        ]);
    }
}
