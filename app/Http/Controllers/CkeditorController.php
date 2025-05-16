<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CkeditorController extends Controller
{
    public function upload(Request $request)
    {

       /*  $request->validate([
            'upload' => 'required|image|max:2048',  // 2MB max
        ]);*/

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            
            $file->storeAs('public/email-templates', $fileName);
            
            return response()->json([
                'url' => Storage::url('email-templates/' . $fileName),
                'uploaded' => true,
            ]);
        }
        
        return response()->json([
            'uploaded' => false,
            'error' => [
                'message' => 'Failed to upload file',
            ],
        ]);
    }
}