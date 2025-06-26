<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\SupabaseStorageService;

class ImageService
{
    protected $supabaseStorage;

    public function __construct(SupabaseStorageService $supabaseStorage = null)
    {
        $this->supabaseStorage = $supabaseStorage ?? new SupabaseStorageService();
    }

    /**
     * Upload images for a model.
     *
     * @param Model $model
     * @param array $images
     * @param string $folder
     * @param string|null $disk
     * @return array
     */
    public function uploadImages(Model $model, array $images, string $folder, ?string $disk = null): array
    {
        $uploaded_images = [];
        $existing_images = $model->images ?? [];
        $disk = $disk ?? $this->getDefaultDisk();

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
            try {
                $uploadResult = null;
                $success = false;

                if ($disk === 'supabase') {
                    // Use Supabase direct upload as primary method
                    $uploadResult = $this->supabaseStorage->uploadFile($image, $path);
                    $success = $uploadResult !== null;

                    if (!$success) {
                        // Only try the S3 adapter as a fallback if direct upload fails
                        try {
                            $success = Storage::disk($disk)->put($path, file_get_contents($image->getRealPath()));

                            if ($success) {
                                $uploadResult = [
                                    'id' => Str::random(8),
                                    'path' => $path,
                                    'disk' => $disk,
                                    'url' => $this->getImageUrl($path, $disk)
                                ];
                            }
                        } catch (\Exception $e) {
                            $success = false;
                        }
                    }
                } else {
                    // Use regular Laravel storage
                    $success = Storage::disk($disk)->put($path, file_get_contents($image->getRealPath()));

                    if ($success) {
                        $uploadResult = [
                            'id' => Str::random(8),
                            'path' => $path,
                            'disk' => $disk,
                            'url' => $this->getImageUrl($path, $disk)
                        ];
                    }
                }

                // If not successful, skip
                if (!$success) {
                    continue;
                }

                $uploaded_images[] = $uploadResult;
            } catch (\Exception $e) {
                continue;
            }
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
        $disk_to_use = null;

        // Check if identifier is an ID or a path
        $is_path = Str::contains($identifier, '/');

        // Find and remove the image
        $updated = array_values(array_filter($images, function($image) use ($identifier, $is_path, &$path_to_delete, &$disk_to_use) {
            // Match by ID (preferred) or by path
            $should_delete = ($is_path && isset($image['path']) && $image['path'] === $identifier) ||
                            (!$is_path && isset($image['id']) && $image['id'] === $identifier);

            if ($should_delete) {
                // Store the path for deletion
                $path_to_delete = $image['path'];
                // Get the disk if available, fallback to default
                $disk_to_use = $image['disk'] ?? $this->getDefaultDisk();
                return false; // Remove from array
            }
            return true; // Keep in array
        }));

        // Delete the file and update model if an image was found
        if ($path_to_delete) {
            // Use regular Laravel storage for all disk types
            Storage::disk($disk_to_use)->delete($path_to_delete);

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

    /**
     * Get the default storage disk.
     *
     * @return string
     */
    public function getDefaultDisk(): string
    {
        return config('filesystems.default', 'supabase');
    }

    /**
     * Get the URL for an image.
     *
     * @param string $path
     * @param string $disk
     * @return string
     */
    public function getImageUrl(string $path, string $disk): string
    {
        try {
            if ($disk === 'public') {
                return asset('storage/' . $path);
            } else if ($disk === 'supabase') {
                $supabaseUrl = env('SUPABASE_URL');
                $bucket = env('SUPABASE_BUCKET', 'tsimpologion');
                return "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}";
            }

            return Storage::disk($disk)->url($path);
        } catch (\Exception $e) {
            return '/storage/' . $path; // Fallback
        }
    }
}
