<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Validator;

/**
 * Class UploadController.
 */
class UploadController extends Controller
{
    /**
     * @return json
     * 
     */
    protected $validation_error = [];

    public function upload(Request $request)
    {
        $file = $request->file('file');
        $moduleName = $request->input('moduleName');
        $field = $request->input('field');

        $valid = $this->validateUpload($file, $moduleName, $field);

        if($valid){
            $imagename = substr(md5($request->input('module')), 0, 8).'-'.str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
    
            $destinationPath = storage_path('images/'.$moduleName);
    
            $file->move($destinationPath, $imagename);
    
            return response()->json(['message' => 'File successfully uploaded', 'filename' => $imagename, 'status' => 200], 200);
        }
        return response()->json(['message' => $this->validation_error, 'filename' => false, 'status' => 415], 200);
    }

    public function delete(Request $request)
    {
        $moduleName = $request->input('modelName');
        $modelToCall = "App\\Models\\" . ucfirst($moduleName);
        $model = $modelToCall::find($request->input('modelId'));
        $filename = $model->image;

        $filePath = public_path('images/'.$moduleName.'/'.$filename);

        try {
            unlink($filePath);
        } catch (\Exception $ex) {
            return response()->json(['message' => 'File removal failed'], 200);
        }

        $model->image = null;
        $model->save();
        return response()->json(['message' => 'File successfully removed'], 200);
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

    protected function validateUpload($file, $moduleName, $field)
    {

        $moduleData = config('laragen.modules')[str_plural($moduleName)];
        $rules = $moduleData[$field];

        $file = array($field => $file);

        $validator = Validator::make($file , [
            $field => $rules,
        ]);

        if ($validator->passes())
        {
            return true; 
        }
        $this->validation_error = $validator->errors()->all();
        return false;
    }
}
