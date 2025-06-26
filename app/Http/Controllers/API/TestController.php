<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestController extends Controller
{    public function testS3Upload(Request $request)
    {
        try {
            // Check if we have a file
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file was uploaded'
                ]);
            }            $file = $request->file('file');
            $path = 'test/' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // First try the direct API method (known to be working)
            $service = new \App\Services\SupabaseStorageService();
            $result = $service->uploadFile($file, $path);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded successfully via direct API',
                    'data' => $result
                ]);
            }

            // If direct API fails, try the S3 adapter as fallback
            try {
                $uploaded = Storage::disk('supabase')->put($path, file_get_contents($file));

                if (!$uploaded) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload file to Supabase'
                    ]);
                }

                // Get the URL
                $url = Storage::disk('supabase')->url($path);

                // Manually construct the URL as a backup
                $manualUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . env('SUPABASE_BUCKET', 'tsimpologion') . '/' . $path;                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded successfully via S3 adapter',
                    'data' => [
                        'path' => $path,
                        'url' => $url,
                        'manualUrl' => $manualUrl
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Both direct API and S3 adapter failed',
                    'direct_api_error' => 'Direct API upload failed first',
                    's3_error' => $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
      public function testDirectUpload(Request $request)
    {
        try {
            // Check if we have a file
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file was uploaded'
                ]);
            }

            $file = $request->file('file');
            $path = 'test/' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Create a direct upload to Supabase using REST API
            $supabaseUrl = env('SUPABASE_URL');
            $supabaseBucket = env('SUPABASE_BUCKET', 'tsimpologion');
            $supabaseKey = env('SUPABASE_SECRET'); // Service role key
              // Create the endpoint URL
            $endpoint = "{$supabaseUrl}/storage/v1/object/{$supabaseBucket}/{$path}";
              // Read file into memory - use binary safe mode
            $fileContents = file_get_contents($file->getRealPath());
            $contentType = $file->getMimeType();

            // Upload file
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => "Bearer {$supabaseKey}",
                'Content-Type' => $contentType,
                'x-upsert' => 'true' // Update if exists
            ])->withBody($fileContents, $contentType)
              ->put($endpoint);
              if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload to Supabase API: ' . $response->body()
                ]);
            }

            // Get the public URL
            $publicUrl = "{$supabaseUrl}/storage/v1/object/public/{$supabaseBucket}/{$path}";

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully via direct API',
                'data' => [
                    'path' => $path,
                    'url' => $publicUrl,
                    'responseStatus' => $response->status()
                ]
            ]);        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }    public function testS3Config(Request $request)
    {
        // Get the S3 configuration
        $config = [
            'driver' => config('filesystems.disks.supabase.driver'),
            'endpoint' => config('filesystems.disks.supabase.endpoint'),
            'region' => config('filesystems.disks.supabase.region'),
            'bucket' => config('filesystems.disks.supabase.bucket'),
            'use_path_style_endpoint' => config('filesystems.disks.supabase.use_path_style_endpoint'),
            'url' => config('filesystems.disks.supabase.url'),
            'env_url' => env('SUPABASE_URL'),
            'env_endpoint' => env('SUPABASE_ENDPOINT'),
            'env_bucket' => env('SUPABASE_BUCKET'),
        ];

        // Check if bucket exists
        $service = new \App\Services\SupabaseStorageService();
        $bucketExists = $service->createBucketIfNotExists();

        return response()->json([
            'success' => true,
            'message' => 'S3 configuration',
            'config' => $config,
            'bucket_exists' => $bucketExists
        ]);
    }
}
