<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Filesystem\Filesystem;
use Validator;
use Image;

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
            $imagename = $this->getWritableFilename(str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension(), $moduleName, true);;
    
            $destinationPath = storage_path('images/'.$moduleName);
    
            $file->move($destinationPath, $imagename);
    
            return response()->json(['message' => 'File successfully uploaded', 'filename' => $imagename, 'status' => 200], 200);
        }

        return response()->json(['message' => $this->validation_error, 'filename' => false, 'status' => 500], 200);
    }
    
    public function uploadGallery(Request $request)
    {
        $moduleName = $request->input('moduleName');
        $field = $request->input('field');
        $files = [];

        foreach ($request->file('file') as $file) {
            $imagename = $this->getWritableFilename(str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension(), $moduleName, true);;
            $destinationPath = storage_path('images/'.$moduleName);
            try {
                $file->move($destinationPath, $imagename);
                $files[] = $imagename;
            } catch (\Throwable $th) {
                $error = $th->getMessage();
            }
        }

        if(!isset($error)){
            return response()->json(['message' => 'File successfully uploaded', 'filenames' => $files, 'status' => 200], 200);
        }

        return response()->json(['message' => $error, 'filename' => false, 'status' => 500], 200);
    }

    public function delete(Request $request)
    {
        $moduleName = $request->input('modelName');
        $modelToCall = "App\\Models\\" . ucfirst($moduleName);
        $model = $modelToCall::find($request->input('modelId'));
        $field = $request->input('field');
        $filename = $model->$field;
        $filePath = public_path('images/'.$moduleName.'/'.$filename);
        $fileSystem = new Filesystem;

        try {
            $fileSystem->remove($filePath);
        } catch (\Exception $ex) {
            return response()->json(['message' => 'File removal failed'], 500);
        }

        $model->$field = null;
        $model->save();
        return response()->json(['message' => 'File successfully removed'], 200);
    }

    /**
     * @return string
     */
    public function process($filename, $moduleName)
    {
        $messages=['errors'=>[]];
        $tempFile = storage_path('images/'.$moduleName.'/'.$filename);
        $fileToWrite = $this->getWritableFilename($filename, $moduleName);

        $tempImage = Image::make($tempFile);

        $methodToUse = $tempImage->width() > $tempImage->height() ? 'widen' : 'heighten';

        try {
            $tempImage->$methodToUse(2000, function ($constraint){
                $constraint->upsize();
            })->save($fileToWrite);
            $messages['success']['filename'] = $fileToWrite;
        } catch (\Exception $ex) {
            $messages['errors'][] = [ 'fileError' => $ex->getMessage()];
        }
        return $messages;
    }

    protected function getWritableFilename($filename, $moduleName, $is_storage=false)
    {
        $dir = $is_storage ? $this->getPath(storage_path('images/'.$moduleName)) : $this->getPath(public_path('images/'.$moduleName));
        $path = $dir.'/'.$filename;
        if (file_exists($path)) {
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $filename .= rand(0,9).'.'.pathinfo($path, PATHINFO_EXTENSION);
            return $this->getWritableFilename($filename, $moduleName, $is_storage); 
        }else{
            return $is_storage ? $filename : $path;
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
