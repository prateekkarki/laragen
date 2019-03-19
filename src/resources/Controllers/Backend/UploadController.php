<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class UploadController.
 */
class UploadController extends Controller
{
    /**
     * @return json
     */
    public function upload(Request $request)
    {
        $image = $request->file('file');

        $imagename = $image->getClientOriginalName();

        $destinationPath = storage_path('images');

        $image->move($destinationPath, $imagename);

        return response()->json(['message' => 'File successfully uploaded', 'filename' => $imagename], 200);
    }
}
