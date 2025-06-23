<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SupabaseStorageService
{    protected $supabaseUrl;
    protected $supabaseKey;
    protected $supabaseBucket;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL');
        // Use the service role key for admin operations
        $this->supabaseKey = env('SUPABASE_SECRET');
        $this->supabaseBucket = env('SUPABASE_BUCKET', 'tsimpologion');
    }

    /**
     * Upload a file to Supabase Storage
     *
     * @param UploadedFile $file
     * @param string $path
     * @return array|null
     */    public function uploadFile(UploadedFile $file, string $path): ?array
    {
        try {
            // Create endpoint URL
            $endpoint = "{$this->supabaseUrl}/storage/v1/object/{$this->supabaseBucket}/{$path}";

            // Read file content - use binary safe mode
            $fileContents = file_get_contents($file->getRealPath());
            $contentType = $file->getMimeType();

            // Make sure the bucket exists
            $this->createBucketIfNotExists();

            // Upload file
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->supabaseKey}",
                'Content-Type' => $contentType,
                'x-upsert' => 'true', // Update if exists
            ])->withBody($fileContents, $contentType)
              ->put($endpoint);

            // Check if upload was successful
            if ($response->successful()) {
                // Generate the public URL
                $fileUrl = "{$this->supabaseUrl}/storage/v1/object/public/{$this->supabaseBucket}/{$path}";

                return [
                    'id' => Str::random(8),
                    'path' => $path,
                    'url' => $fileUrl,
                    'disk' => 'supabase'
                ];
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Delete a file from Supabase Storage
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool
    {
        try {
            // Create endpoint URL
            $endpoint = "{$this->supabaseUrl}/storage/v1/object/{$this->supabaseBucket}/{$path}";

            // Delete file
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->supabaseKey}"
            ])->delete($endpoint);

            // Check if deletion was successful
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if a bucket exists, create it if it doesn't
     *
     * @param string $bucketName
     * @param bool $isPublic
     * @return bool
     */
    public function createBucketIfNotExists(string $bucketName = null, bool $isPublic = true): bool
    {
        $bucketName = $bucketName ?? $this->supabaseBucket;

        try {
            // Create endpoint URL for checking bucket
            $endpoint = "{$this->supabaseUrl}/storage/v1/bucket/{$bucketName}";

            // Check if bucket exists
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->supabaseKey}",
                'Content-Type' => 'application/json'
            ])->get($endpoint);

            // If bucket doesn't exist, create it
            if ($response->status() === 404) {
                $createEndpoint = "{$this->supabaseUrl}/storage/v1/bucket";

                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->supabaseKey}",
                    'Content-Type' => 'application/json'
                ])->post($createEndpoint, [
                    'id' => $bucketName,
                    'name' => $bucketName,
                    'public' => $isPublic
                ]);

                return $response->successful();
            } else if ($response->successful()) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
