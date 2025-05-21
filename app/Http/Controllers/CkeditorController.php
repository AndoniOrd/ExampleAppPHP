<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Controller for handling CKEditor image uploads
 * 
 * @route POST /ckeditor/upload - Handles file uploads from CKEditor
 */
class CkeditorController extends Controller
{
    public function upload(Request $request)
    {
        // Log the request to debug
        Log::info('CKEditor upload request received', [
            'has_file' => $request->hasFile('upload'),
            'request_all' => $request->all()
        ]);

        try {
            $request->validate([
                'upload' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                $fileName = Str::random(40).'.'.$file->extension();
                
                // Store using public disk
                $path = $file->storeAs('email-templates', $fileName, 'public');
                
                return response()->json([
                    'uploaded' => true,
                    'url' => Storage::disk('public')->url($path)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error uploading file', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'uploaded' => false,
                'error' => [
                    'message' => 'Error uploading file: ' . $e->getMessage(),
                ],
            ], 500);
        }

        return response()->json([
            'uploaded' => false,
            'error' => [
                'message' => 'No file was uploaded',
            ],
        ], 400);
    }
}