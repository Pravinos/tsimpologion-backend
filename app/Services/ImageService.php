<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Upload images for a model.
     *
     * @param Model $model
     * @param array $images
     * @param string $folder
     * @return array
     */
    public function uploadImages(Model $model, array $images, string $folder): array
    {
        $uploaded_images = [];
        $existing_images = $model->images ?? [];

        foreach ($images as $image) {
            if (!($image instanceof UploadedFile)) {
                continue;
            }

            // Get file extension
            $extension = $image->getClientOriginalExtension();

            // Create a shorter unique filename using Str::random
            $filename = Str::random(10) . '.' . $extension;

            // Create a more compact path
            $path = "{$folder}/{$model->id}/{$filename}";

            // Store the file
            if (!Storage::disk('public')->put($path, file_get_contents($image))) {
                continue;
            }

            $uploaded_images[] = [
                'id' => Str::random(8), // Add a short ID for easier reference
                'path' => $path,
                'url' => asset('storage/' . $path)
            ];
        }

        // Merge with existing images
        $all_images = array_merge($existing_images, $uploaded_images);

        // Update the model
        $model->images = $all_images;
        $model->save();

        return $uploaded_images;
    }

    /**
     * Delete an image from a model.
     *
     * @param Model $model
     * @param string $identifier
     * @return bool
     */
    public function deleteImage(Model $model, string $identifier): bool
    {
        $images = $model->images ?? [];
        $path_to_delete = null;

        // Check if identifier is an ID or a path
        $is_path = Str::contains($identifier, '/');

        // Find and remove the image
        $updated = array_values(array_filter($images, function($image) use ($identifier, $is_path, &$path_to_delete) {
            // Match by ID (preferred) or by path
            $should_delete = ($is_path && isset($image['path']) && $image['path'] === $identifier) ||
                            (!$is_path && isset($image['id']) && $image['id'] === $identifier);

            if ($should_delete) {
                // Store the path for deletion
                $path_to_delete = $image['path'];
                return false; // Remove from array
            }
            return true; // Keep in array
        }));

        // Delete the file and update model if an image was found
        if ($path_to_delete) {
            Storage::disk('public')->delete($path_to_delete);
            $model->images = $updated;
            $model->save();
            return true;
        }

        return false;
    }

    /**
     * Get image by ID
     */
    public function getImageById(Model $model, string $image_id): ?array
    {
        $images = $model->images ?? [];

        foreach ($images as $image) {
            if (isset($image['id']) && $image['id'] === $image_id) {
                return $image;
            }
        }

        return null;
    }
}
