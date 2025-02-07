<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('q');
        $validSortColumns = ['menu',"price"];
        $sortBy = in_array($request->input('sort_by'), $validSortColumns, true) ? $request->input('sort_by') : 'id';
        $sortDirection = in_array($request->input('sort_direction'), ['asc', 'desc'], true) ? $request->input('sort_direction') : 'desc';
        $limit = $request->input('limit', 5);

        $limit = is_numeric($limit) && $limit > 0 && $limit <= 100 ? (int)$limit : 5;

        $query = Menu::query();

        if ($searchTerm) {
                $query->where('menu', 'like', '%' . $searchTerm . '%');
        }

        $query->orderBy($sortBy, $sortDirection);

        $menu = $query->paginate($limit);

        $menu->appends([
            'q' => $searchTerm,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'limit' => $limit,
        ]);

        return MenuResource::collection($menu);
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
    public function store(StoreMenuRequest $request)
    {

        $image = $request->file("image");

        $imageName = "menu" . uniqid() . '.' . $image->extension();
        $imagePath = $image->storeAs("images/menuImage/", $imageName,"public");

        $imageURL =  asset('storage/' . $imagePath);


        $menu = new Menu();
        $menu->category_id = $request->category_id;
        $menu->price = $request->price;
        $menu->image = $imageURL;
        $menu->menu = $request->menu;
        $menu->save();

        return response()->json([
            'message' => 'Menu created successfully',
            'data' => new MenuResource($menu)
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Menu $menu)
    {

        $menu->category_id = $request->category_id;
        $menu->price = $request->price;
        $menu->menu = $request->menu;
        $menu->save();

        return response()->json([
            'message' => 'Menu updated successfully',
            'data' => new MenuResource($menu)
        ], 201);
    }

    public function updateImage(Request $request,$id){

        $request->validate(["image" => "required"]);

        $validated = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:menus,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Invalid menu ID'
            ], 404);
        }

        $menu = Menu::find($id);

        $imagePath = str_replace(asset('storage'), '', $menu->image);

        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        $image = $request->file("image");

        $imageName = "menu" . uniqid() . '.' . $image->extension();
        $imagePath = $image->storeAs("images/menuImage/", $imageName,"public");

        $imageURL =  asset('storage/' . $imagePath);

        $menu->image = $imageURL;

        return response()->json([
            'message' => 'Menu image updated successfully',
            'data' => new MenuResource($menu)
        ], 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $validated = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:menus,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Invalid menu ID'
            ], 404);
        }

        $menu = Menu::find($id);

        $menu->delete();

        return response()->json([
            'message' => 'Menu deleted successfully'
        ]);

    }
}
