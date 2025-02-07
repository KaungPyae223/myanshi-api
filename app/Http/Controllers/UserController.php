<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function search(Request $request)
    {
        $search = $request->input('query');
        $users = User::where('name', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->paginate(15);

        if ($users->isEmpty()) {
            return response()->json([
                'message' => 'No users found'
            ], 404);
        }

        return response()->json([
            'users' => UserResource::collection($users),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
                'path' => $users->path(),
                'links' => $users->toArray()['links'] ?? [],
            ],
            'message' => 'Users fetched successfully!'
        ], 200);
    }

    public function index()
    {
        $users = User::paginate(15);

        if ($users->isEmpty()) {
            return response()->json([
                'message' => 'No users found'
            ], 404);
        }

        return response()->json([
            'users' => UserResource::collection($users),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
                'path' => $users->path(),
                'links' => $users->toArray()['links'] ?? [],
            ],
            'message' => 'Users fetched successfully!'
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
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'phone_number' => 'nullable|string|min:10|max:20',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'address' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'role' => 'nullable|in:user,admin'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
                'phone_number' => $request->phone_number,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'role' => $request->role
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $newImageName = 'user-' . time() . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('user_images/', $newImageName, 'public');
                UserImage::create([
                    'user_id' => $user->id,
                    'image_path' => $newImageName
                ]);
            }
            return response()->json([
                'user' => new UserResource($user),
                'message' => 'User created successfully!'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User creation failed!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json([
            'user' => new UserResource($user),
            'message' => 'User fetched successfully!'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $id,
                'password' => 'sometimes|required|string|min:6',
                'phone_number' => 'sometimes|nullable|string|min:10|max:20',
                'date_of_birth' => 'sometimes|nullable|date',
                'gender' => 'sometimes|nullable|in:male,female,other',
                'address' => 'sometimes|nullable|string|max:255',
                'role' => 'sometimes|nullable|in:user,admin'
            ]);

            if ($request->filled('password')) {
                $validatedData['password'] = Hash::make($request->password);
            }

            $user->update($validatedData);

            return response()->json([
                'user' => new UserResource($user),
                'message' => 'User updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User update failed!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfileImage(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $oldImage = UserImage::where('user_id', $user->id)->first();

        if ($oldImage) {
            Storage::disk('public')->delete('user_images/' . $oldImage->image_path);
            $oldImage->delete();
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $newImageName = 'user-' . time() . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('user_images/', $newImageName, 'public');

            UserImage::create([
                'user_id' => $user->id,
                'image_path' => $newImageName
            ]);
        }

        return response()->json([
            'message' => 'Profile image updated successfully!'
        ], 200);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $oldImage = UserImage::where('user_id', $user->id)->first();

        if ($oldImage) {
            Storage::disk('public')->delete('user_images/' . $oldImage->image_path);
            $oldImage->delete();
        }
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully!'
        ], 200);
    }
}
