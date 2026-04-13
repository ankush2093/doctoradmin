<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;

class GalleryController extends Controller
{
    // Create new gallery item
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'photo' => 'required|file|mimes:jpeg,png,jpg,gif,webp,svg,svgz,bmp,tif,tiff,avif,ico',
                'photoAlt' => 'nullable|string',
                'photoHeading' => 'nullable|string',
                'photoDescription' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $imgName = null;
            $imgUrl = null;

            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $sanitizedBaseName = preg_replace('/\s+/', '_', $name);
                $currentDateTime = now()->format('Y-m-d_H-i-s');
                $extension = strtolower($image->getClientOriginalExtension());
                $filename = $sanitizedBaseName . '_' . $currentDateTime . '.' . $extension;
                $imagePath = $image->storeAs('uploads/gallery', $filename, 'public');
                $imgUrl = '/storage/uploads/gallery/' . $filename;
                $imgName = $filename;
            }

            $data = [
                'photo' => $imgName,
                'photoUrl' => $imgUrl,
                'photoAlt' => $request->photoAlt,
                'photoHeading' => $request->photoHeading,
                'photoDescription' => $request->photoDescription,
                'isActive' => true,
            ];
            $gallery = Gallery::create($data);

            return response()->json(['success' => true, 'message' => 'Gallery photo uploaded successfully.', 'gallery' => $gallery], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Update gallery item
    public function update(Request $request, $id)
    {
        try {
            $gallery = Gallery::where('id', $id)->where('isActive', true)->first();
            if (!$gallery) {
                return response()->json(['success' => false, 'message' => 'Gallery item not found or inactive'], 404);
            }

            $validator = Validator::make($request->all(), [
                'photo' => 'sometimes|file|mimes:jpeg,png,jpg,gif,webp,svg,svgz,bmp,tif,tiff,avif,ico',
                'photoAlt' => 'nullable|string',
                'photoHeading' => 'nullable|string',
                'photoDescription' => 'nullable|string',
                'isActive' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            if ($request->hasFile('photo')) {
                if ($gallery->photo && Storage::disk('public')->exists('uploads/gallery/' . $gallery->photo)) {
                    Storage::disk('public')->delete('uploads/gallery/' . $gallery->photo);
                }

                $image = $request->file('photo');
                $filename = 'gallery_' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('uploads/gallery', $filename, 'public');
                $gallery->photo = $filename;
                $gallery->photoUrl = '/storage/uploads/gallery/' . $filename;
            }

            $gallery->update($request->except('photo'));

            return response()->json(['success' => true, 'message' => 'Gallery item updated successfully.', 'gallery' => $gallery], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // List all gallery items
    public function getAll()
    {
        try {
            $galleries = Gallery::where('isActive', true)->get();

            $galleries->transform(function ($gallery) {
                $gallery->photoUrl = $gallery->photoUrl ? url($gallery->photoUrl) : null;
                return $gallery;
            });

            return response()->json(['success' => true, 'message' => 'Gallery retrieved successfully.', 'galleries' => $galleries], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Show gallery item by id
    public function getById($id)
    {
        try {
            $gallery = Gallery::where('id', $id)->where('isActive', true)->first();
            if (!$gallery) {
                return response()->json(['success' => false, 'message' => 'Gallery item not found or inactive'], 404);
            }

            $gallery->photoUrl = $gallery->photoUrl ? url($gallery->photoUrl) : null;

            return response()->json(['success' => true, 'message' => 'Gallery item retrieved successfully.', 'gallery' => $gallery], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Delete gallery item
    public function delete($id)
    {
        try {
            $gallery = Gallery::find($id);
            if (!$gallery) {
                return response()->json(['success' => false, 'message' => 'Gallery item not found'], 404);
            }

            if ($gallery->photo && Storage::disk('public')->exists('uploads/gallery/' . $gallery->photo)) {
                Storage::disk('public')->delete('uploads/gallery/' . $gallery->photo);
            }

            $gallery->update(['isActive' => false]);

            return response()->json(['success' => true, 'message' => 'Gallery item deleted successfully.'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
