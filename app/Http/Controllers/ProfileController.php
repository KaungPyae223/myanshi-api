<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\UserImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'user' => new UserResource($user),
            'message' => 'User profile retrieved successfully',
        ], 200);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone_number' => 'sometimes|nullable|string|min:10|max:20',
            'date_of_birth' => 'sometimes|nullable|date',
            'gender' => 'sometimes|nullable|in:male,female,other',
            'address' => 'sometimes|nullable|string|max:255',
        ]);

        $user->update($validated);
        return response()->json([
            'user' => new UserResource($user),
            'message' => 'Profile updated successfully!'
        ], 200);
    }

    public function changeProfileImage(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password changed successfully!'
        ], 200);
    }
}
