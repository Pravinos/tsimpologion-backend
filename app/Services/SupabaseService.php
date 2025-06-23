<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected $supabaseUrl;
    protected $supabaseKey;
    protected $storageEndpoint;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL');
        $this->supabaseKey = env('SUPABASE_SECRET');  // Service role key for admin operations
        $this->storageEndpoint = "{$this->supabaseUrl}/storage/v1";
    }

    /**
     * Create a bucket if it doesn't exist
     */
    public function createBucketIfNotExists(string $bucketName, bool $isPublic = true)
    {
        try {
            // First check if bucket exists
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->supabaseKey}",
                'Content-Type' => 'application/json'
            ])->get("{$this->storageEndpoint}/bucket/{$bucketName}");

            // If bucket doesn't exist (404), create it
            if ($response->status() === 404) {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->supabaseKey}",
                    'Content-Type' => 'application/json'
                ])->post("{$this->storageEndpoint}/bucket", [
                    'name' => $bucketName,
                    'public' => $isPublic,
                ]);

                if ($response->successful()) {
                    // Set bucket policy for public access if needed
                    if ($isPublic) {
                        $this->setPublicBucketPolicy($bucketName);
                    }

                    return true;
                } else {
                    return false;
                }
            } else if ($response->successful()) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Set public access policy for a bucket
     */
    private function setPublicBucketPolicy(string $bucketName)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->supabaseKey}",
                'Content-Type' => 'application/json'
            ])->put("{$this->storageEndpoint}/bucket/{$bucketName}", [
                'public' => true
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
