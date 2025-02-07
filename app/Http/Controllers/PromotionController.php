<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(Request $request){

        $searchTerm = $request->input('q');
        $validSortColumns = ['menu',"price","discount"];
        $sortBy = in_array($request->input('sort_by'), $validSortColumns, true) ? $request->input('sort_by') : 'id';
        $sortDirection = in_array($request->input('sort_direction'), ['asc', 'desc'], true) ? $request->input('sort_direction') : 'desc';
        $limit = $request->input('limit', 5);

        $limit = is_numeric($limit) && $limit > 0 && $limit <= 100 ? (int)$limit : 5;

        $query = Menu::query()->whereNotNull("start_date");

        if ($searchTerm) {
                $query->where('menu', 'like', '%' . $searchTerm . '%');
        }

        $query->orderBy($sortBy, $sortDirection);

        $promotion = $query->paginate($limit);

        $promotion->appends([
            'q' => $searchTerm,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'limit' => $limit,
        ]);

        return PromotionResource::collection($promotion);

    }
    public function createPromotion(PromotionRequest $request,$id){

        $menu = Menu::find($id);

        $menu->start_date = $request->start_date;
        $menu->end_date = $request->end_date;
        $menu->discount = $request->discount;

        $menu->update();

        return response()->json([
            'message' => 'Promotion created successfully',
            'data' => new PromotionResource($menu)
        ], 201);

    }
}
