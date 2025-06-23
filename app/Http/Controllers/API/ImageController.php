<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    protected $image_service;

    public function __construct(ImageService $image_service)
    {
        $this->image_service = $image_service;
    }

    /**
     * Upload images for a model.
     */
    public function upload(Request $request, string $model_type, int $id): JsonResponse
    {
        // Validate input
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:8000'
        ]);

        // Get model
        $model = $this->getModel($model_type, $id);
        if (!$model) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        // Check permissions
        if (!$model->userCanManageImages(auth()->user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Upload images
        $uploaded_images = $this->image_service->uploadImages(
            $model,
            $request->file('images'),
            $model->getImageFolder()
        );

        return response()->json([
            'message' => 'Images uploaded successfully',
            'data' => [
                'images' => $uploaded_images,
                'all_images' => $model->images
            ]
        ], 200);
    }

    /**
     * Delete an image from a model.
     */
    public function delete(string $model_type, int $id, string $image_id): JsonResponse
    {
        // Get model
        $model = $this->getModel($model_type, $id);
        if (!$model) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        // Check permissions
        if (!$model->userCanManageImages(auth()->user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete image
        $success = $this->image_service->deleteImage($model, $image_id);

        if ($success) {
            return response()->json([
                'message' => 'Image deleted successfully',
                'data' => ['images' => $model->images]
            ], 200);
        }

        return response()->json([
            'message' => 'Image not found'
        ], 404);
    }

     /**
     * View all images for a model.
     */
    public function viewAll(string $model_type, int $id): JsonResponse
    {
        // Get model
        $model = $this->getModel($model_type, $id);
        if (!$model) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        // No images available
        if (empty($model->images)) {
            return response()->json([
                'message' => 'No images found for this resource',
                'data' => ['images' => []]
            ], 200);
        }

        // Return all images
        return response()->json([
            'message' => 'Images retrieved successfully',
            'data' => [
                'model_type' => $model_type,
                'model_id' => $id,
                'images' => $model->images
            ]
        ], 200);
    }

    /**
     * View a specific image from a model.
     */
    public function viewOne(string $model_type, int $id, string $image_id): JsonResponse
    {
        // Get model
        $model = $this->getModel($model_type, $id);
        if (!$model) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        // Find the specific image
        $image = $this->image_service->getImageById($model, $image_id);

        if (!$image) {
            return response()->json([
                'message' => 'Image not found',
                'debug_info' => [
                    'model_type' => $model_type,
                    'model_id' => $id,
                    'image_id' => $image_id,
                    'available_images' => $model->images
                ]
            ], 404);
        }

        // Return the specific image
        return response()->json([
            'message' => 'Image retrieved successfully',
            'data' => [
                'model_type' => $model_type,
                'model_id' => $id,
                'image' => $image
            ]
        ], 200);
    }

    /**
     * Get the model for the given type and ID.
     */
    protected function getModel(string $model_type, int $id): ?Model
    {
        $model_map = [
            'food-spots' => \App\Models\FoodSpot::class,
            'users' => \App\Models\User::class,
            'reviews' => \App\Models\Review::class,
        ];

        if (!isset($model_map[$model_type])) {
            return null;
        }

        $model_class = $model_map[$model_type];
        return $model_class::find($id);
    }
}
