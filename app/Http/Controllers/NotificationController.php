<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Throwable;

class NotificationController extends Controller
{
    var $messaging;

    public function __construct()
    {
        $this->loadSettings();
    }

    private function loadSettings()
    {
        $path = storage_path("notifications-admin.json");
        Log::info("path:$path");
        $factory = (new Factory)->withServiceAccount($path);
        $this->messaging = $factory->createMessaging();
    }

    // Show form
    public function create()
    {
        return view('notifications.create');
    }
    

    public function sendToAllUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:220',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'image_url' => 'nullable|url',
            'type' => 'nullable|string',
            'id' => 'nullable|string',
            'deep_link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
       
        $topic = $request->input('channel', 'datartech');
        $imageUrl = null;

        if ($request->hasFile('image')) {
            try {
                $folder = public_path('uploads/notifications');
                if (!file_exists($folder)) {
                    mkdir($folder, 0755, true);
                }
                
                $file = $request->file('image');
                $originalExtension = strtolower($file->getClientOriginalExtension());
                $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $originalPath = $folder . '/' . $filename . '.' . $originalExtension;
                
                $fileSize = $file->getSize();
                $file->move($folder, $filename . '.' . $originalExtension);
                
                if ($fileSize > 61440) {
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($originalPath);
                    
                    $image->scaleDown(width: 300);
                    
                    $avatarFilename = 'avatar_' . $filename . '.jpg';
                    $avatarPath = $folder . '/' . $avatarFilename;
                    
                    $image->toJpeg(quality: 75);
                    $image->save($avatarPath);
                    if (filesize($avatarPath) > 61440) {
                        $image->toJpeg(quality: 60);
                        $image->save($avatarPath);
                        if (filesize($avatarPath) > 61440) {
                            $image->scaleDown(width: 200);
                            $image->toJpeg(quality: 60);
                            $image->save($avatarPath);
                            if (filesize($avatarPath) > 61440) {
                                $image->scaleDown(width: 150);
                                $image->toJpeg(quality: 50);
                                $image->save($avatarPath);
                            }
                        }
                    }
                    
                    $avatarUrl = asset('uploads/notifications/' . $avatarFilename);
                    Log::info('Avatar created as JPG: ' . $avatarUrl . ' (Size: ' . filesize($avatarPath) . ' bytes)');
                }
                
                if(isset($avatarUrl)){
                    $imageUrl = $avatarUrl;
                }else{
                    $imageUrl = asset('uploads/notifications/' . $filename . '.' . $originalExtension);
                }
                
                Log::info('Image uploaded successfully: ' . $imageUrl);
                
            } catch (Throwable $e) {
                Log::error('Image upload failed: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
                return back()->with('error', 'Failed to upload image: ' . $e->getMessage())->withInput();
            }
        }
        elseif ($request->filled('image_url')) {
            $imageUrl = $request->input('image_url');
        }
        else {
            $imageUrl = null;
        }
        $data = [
            'type' => $request->input('type', 'general'),
            'id' => $request->input('id', ''),
            'deep_link' => $request->input('deep_link', ''),
            'timestamp' => now()->timestamp,
            'click_action' => 'OPEN_MAIN',
            'image_url' => $imageUrl,
            'image'=> $imageUrl,
        ];

        $data = array_filter($data);
        
        Log::info('Notification data: ' , $data);

        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(Notification::create(
                $request->input('title'),
                $request->input('body')
            ))
            ->withData($data);

        try {
            $response = $this->messaging->send($message);
            Log::info('Notification sent successfully:', ['response' => $response]);            
            return back()->with([
                'success' => 'Notification sent successfully to all users!',
                'image_url' => $imageUrl 
            ]);            
        } catch (\Throwable $e) {
            Log::error('Failed to send notification:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to send notification: ' . $e->getMessage())->withInput();
        }
    }

    public function sendToDeviceTokens($tokens)
    {
        $message = CloudMessage::new()
            ->withNotification(Notification::create(
                'Personal Message',
                'Hello from server!'
            ))
            ->withData(['type' => 'personal']);
        
        $report = $this->messaging->sendMulticast($message, $tokens);
        
        Log::info("Successful sends: " . $report->successes()->count());
        Log::info("Failed sends: " . $report->failures()->count());
        
        if ($report->hasFailures()) {
            foreach ($report->failures()->getItems() as $failure) {
                Log::error($failure->error()->getMessage());
            }
        }
    }

    public function listUploadedImages()
    {
        $folder = public_path('uploads/notifications');

        $files = scandir($folder);

        $images = [];
        
        foreach ($files as $file) {
            $images[] = [
                'url' => asset('uploads/' . $file),
                'path' => $file,
                'name' => basename($file),
                'size' => Storage::disk('public')->size($file),
                'last_modified' => Storage::disk('public')->lastModified($file),
            ];
        }
        
        return view('notifications.images', compact('images'));
    }

    public function deleteImage(Request $request, $filename)
    {
        try {
            $path = 'notifications/' . $filename;
            
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                return back()->with('success', 'Image deleted successfully');
            }
            
            return back()->with('error', 'Image not found');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete image: ' . $e->getMessage());
        }
    }
}