<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ResourceDownloadController extends Controller
{
    public function download(Request $request, $resourceSlug)
    {
        try {
            $resource = Resource::firstWhere('slug', $resourceSlug);
            
            if (!$resource) {
                abort(404, 'Resource not found.');
            }
            
            // Permission checks
            if (!Auth::check()) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Please login to download this resource.'], 403);
                }
                return redirect()->route('login')->with('error', 'Please login to download this resource.');
            }
            
            $user = Auth::user();
            
            // Check if user has access
            $hasPurchased = $user->orders()
                ->where('resource_id', $resource->id)
                ->where('payment_status', 'paid')
                ->exists();
                
            $isSubscribed = $user->subscriptions()->active()->exists();
            $isFree = $resource->price == 0;
            
            if (!$hasPurchased && !$isSubscribed && !$isFree) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'You have not purchased this resource.'], 403);
                }
                abort(403, 'You have not purchased this resource.');
            }
            
            // Check download limits
            $limitReached = hasHitDownloadLimit($resource->id);
            if ($limitReached) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => $limitReached], 403);
                }
                abort(403, $limitReached);
            }
            
            

            // Process download based on delivery type
            if ($resource->delivery_type === 'upload' && !empty($resource->file_path)) {
                $filePath = $resource->file_path;
                
                if (!Storage::disk('public')->exists($filePath)) {
                    if ($request->wantsJson()) {
                        return response()->json(['error' => 'File not found. Please contact support.'], 404);
                    }
                    abort(404, 'File not found. Please contact support.');
                }
                
                // Track download
                $this->markDownloaded($resource);
                
                // Get the actual file extension
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                
                // Create clean filename from resource title
                $cleanFilename = Str::slug($resource->title) . '.' . $extension;
                
                // Return file download with custom filename
                return Storage::disk('public')->download($filePath, $cleanFilename, [
                    'Content-Type' => Storage::disk('public')->mimeType($filePath),
                    'Content-Disposition' => "attachment; filename=\"{$cleanFilename}\"",
                    'X-Filename' => $cleanFilename,
                ]);
                
            } elseif ($resource->delivery_type === 'url' && !empty($resource->external_url)) {
                // Track download
                $this->markDownloaded($resource);
                
                $extension = pathinfo(parse_url($resource->external_url, PHP_URL_PATH), PATHINFO_EXTENSION);
                $cleanFilename = Str::slug($resource->title) . '.' . ($extension ?: 'file');
                
                
                // Stream the external file
                $fileContent = file_get_contents($resource->external_url);
                return response($fileContent, 200)
                    ->header('Content-Type', mime_content_type($resource->external_url))
                    ->header('Content-Disposition', "attachment; filename=\"{$cleanFilename}\"");
            }
            
            abort(404, 'Resource not available for download.');
            
        } catch (\Exception $e) {
            Log::error('Download error: ' . $e->getMessage(), [
                'resource_id' => $resourceId ?? null,
                'user_id' => Auth::id(),
                'exception' => $e
            ]);
            
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Download failed. Please try again.'], 500);
            }
            
            return back()->with('error', 'Download failed. Please try again.');
        }
    }

    public function markDownloaded($resource)
    {
        if (!Auth::check()) return;

        if(empty($resource->file_path) && empty($resource->external_url)){
            return;
        }

        $user = Auth::user();

        $resource->download_count = $resource->download_count + 1;
        $resource->save();

        $userDownload = $user->downloads()->where('resource_id', $resource->id)->first();
        if ($userDownload) {
            $userDownload->download_count = $userDownload->download_count + 1;
            $userDownload->downloaded_at = now();
            $userDownload->save();
        } else {
            $user->downloads()->create([
                'resource_id' => $resource->id,
                'download_count' => 1,
                'downloaded_at' => now(),
                'access_type' => $user->subscriptions()->active()->exists() ? 'subscription' : 'free',
            ]);
        }

        trackDownload($resource->id);
    }
}