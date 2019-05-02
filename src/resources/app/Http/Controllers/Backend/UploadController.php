<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Filesystem\Filesystem;
use Validator;
use Image;
use Storage;

/**
 * Class UploadController.
 */
class UploadController extends Controller
{
    function __construct()
    {
        $this->thumbnail_sizes = [
            'sm' => config('laragen.options.image_sizes.sm'),
            'md' => config('laragen.options.image_sizes.md'),
            'xs' => config('laragen.options.image_sizes.xs'),
        ];
    }

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

        if ($valid) {
            $imagename = $this->getWritableFilename(str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension(), $moduleName, true);
    
            $destinationPath = storage_path('temp/'.$moduleName);
    
            $file->move($destinationPath, $imagename);
            
            // Storage::put('/public/'.$moduleName .'/'. $imagename, $file );

    
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
            $imagename = $this->getWritableFilename(str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension(), $moduleName, true); ;
            $destinationPath = storage_path('temp/'.$moduleName);
            try {
                $file->move($destinationPath, $imagename);
                $files[] = $imagename;
            } catch (\Throwable $th) {
                $error = $th->getMessage();
            }
        }

        if (!isset($error)) {
            return response()->json(['message' => 'File successfully uploaded', 'filenames' => $files, 'status' => 200], 200);
        }

        return response()->json(['message' => $error, 'filename' => false, 'status' => 500], 200);
    }

    public function delete(Request $request)
    {
        $moduleName = $request->input('modelName');
        $modelToCall = "App\\Models\\".ucfirst($moduleName);
        $model = $modelToCall::find($request->input('modelId'));
        $field = $request->input('field');
        $filename = $model->$field;
        $filePath = public_path('temp/'.$moduleName.'/'.$filename);
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
        
        $messages = ['errors'=>[]];
        $filePath = storage_path('temp/'.$moduleName.'/'.$filename);
        $filenameToStore=$this->getFilenameToStore($filename);
        
        foreach ($this->thumbnail_sizes as $thumbType => $thumbSizes)
        {
            $thumbDir          = $thumbType;
            [$width, $height]  = explode('x', $thumbSizes);

            $img = Image::make($filePath)->resize($width, $height, function($constraint) {
                $constraint->aspectRatio();
            });

            if (! is_dir(storage_path("app/public/images/".$moduleName.'/'.$thumbDir))) {
                @mkdir(storage_path("app/public/images/".$moduleName.'/'.$thumbDir), 0777, true);
            }

            $thumbnailpath = storage_path(("app/public/images/".$moduleName.'/'.$thumbDir.'/'.$filenameToStore));

            try {
                $img->save($thumbnailpath);
                $messages['filename'] = $filenameToStore;
            } catch (\Exception $ex) {
                $messages['errors'][] = ['fileError' => $ex->getMessage()];
            }
        }
        return $messages;
    }

    public function getThumbnailSizeFor($type)
    {
        return explode('x', $this->thumbnail_sizes[$type]);
    }

    protected function getWritableFilename($filename, $moduleName, $is_storage = false)
    {
        $dir = $is_storage ? $this->getPath(storage_path('temp/'.$moduleName)) : $this->getPath(storage_path('app/public/'.$moduleName));
        $path = $dir.'/'.$filename;
        if (file_exists($path)) {
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $filename .= rand(0, 9).'.'.pathinfo($path, PATHINFO_EXTENSION);
            return $this->getWritableFilename($filename, $moduleName, $is_storage); 
        } else {
            return $is_storage ? $filename : $path;
        }
    }

    function getFilename($filename)
    {
        return pathinfo($filename, PATHINFO_FILENAME);
    }

    function getFileExtension($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    function getFilenameToStore($filename)
    {
        return $this->getFilename($filename).'_'.uniqid().'.'.$this->getFileExtension($filename);
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

        $validator = Validator::make($file, [
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
