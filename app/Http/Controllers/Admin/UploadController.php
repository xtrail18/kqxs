<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function tinymce(Request $request)
    {
        // CKEditor 5 SimpleUploadAdapter gửi field 'upload'
        $fieldName = $request->hasFile('upload') ? 'upload' : 'file';

        $request->validate([
            $fieldName => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
        ]);

        $file = $request->file($fieldName);
        $name = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('public/uploads/tinymce', $name);

        $url = asset(str_replace('public/', 'storage/', $path));

        // CKEditor 5 cần JSON có key 'url'
        return response()->json(['url' => $url, 'location' => $url]);
    }
}
