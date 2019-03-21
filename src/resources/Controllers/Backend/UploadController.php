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

        $imagename = substr(md5($module), 0, 8).'-'.str_slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$image->getClientOriginalExtension();

        $destinationPath = storage_path('images/'.$moduleName);

        $image->move($destinationPath, $imagename);

        return response()->json(['message' => 'File successfully uploaded', 'filename' => $imagename], 200);
    }

    /**
     * @return string
     */
    public function process($filename, $moduleName)
    {
        $tempFile = storage_path('images/'.$moduleName.'/'.$filename);
        $fileToWrite = $this->getWritableFilename($filename, $moduleName);
        rename($tempFile, $fileToWrite);
    }

    protected function getWritableFilename($filename, $moduleName)
    {
        $dir = $this->getPath(public_path('images/'.$moduleName));
        $path = $dir.'/'.$filename;
        if (file_exists($path)) {
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $filename .= rand(0,9).'.'.pathinfo($path, PATHINFO_EXTENSION);
            return $this->getWritableFilename($filename, $moduleName); 
        }else{
            return $path;
        }
    }

    protected function getPath($path)
    {
        if (!is_dir($path))
            mkdir($path, 0755, true);

        return $path;
    }
}
