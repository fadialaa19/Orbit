<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ScholarshipRichTextUploadController
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB
        ]);

        $file = $request->file('image');

        $dir = 'scholarships/quill-images'; // per user choice (storage/app/public/...)

        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($dir, $filename, 'public');

        $url = \Storage::disk('public')->url($path);

        // Quill expects { url: "..." }
        return response()->json(['url' => $url], Response::HTTP_OK);
    }
}

