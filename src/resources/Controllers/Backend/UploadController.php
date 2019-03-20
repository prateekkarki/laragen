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
    public function upload(Request $request, $moduleName, $module)
    {
        $image = $request->file('file');

        $imagename = $module . '-' . str_slug($image->getClientOriginalName());

        $destinationPath = storage_path('images/'.$moduleName);

        $image->move($destinationPath, $imagename);

        return response()->json(['message' => 'File successfully uploaded', 'filename' => $imagename], 200);
    }
}
