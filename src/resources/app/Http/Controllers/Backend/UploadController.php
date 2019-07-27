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
    private $thumbnail_sizes;
    private $temp_path;
    private $image_path;
    private $file_path;

    function __construct()
    {
        $this->thumbnail_sizes = [
            'sm' => config('laragen.options.image_sizes.sm'),
            'md' => config('laragen.options.image_sizes.md'),
            'xs' => config('laragen.options.image_sizes.xs'),
        ];

        $this->temp_path = storage_path('temp/');
        $this->image_path = public_path('images/');
        $this->file_path = public_path('files/');
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
    
            $destinationPath = $this->temp_path.$moduleName;
            $file->move($destinationPath, $imagename);
    
            return response()->json(['message' => 'File successfully uploaded', 'filename' => $imagename], 200);
        }

        return response()->json(['message' => $this->validation_error, 'filename' => false], 500);
    }
    
    public function uploadGallery(Request $request)
    {
        $moduleName = $request->input('moduleName');
        $field = $request->input('field');
        $files = [];

        foreach ($request->file('file') as $file) {
            $imagename = $this->getWritableFilename(str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension(), $moduleName, true); ;
            $destinationPath = $this->temp_path.$moduleName;
            try {
                $file->move($destinationPath, $imagename);
                $files[] = $imagename;
            } catch (\Throwable $th) {
                $error = $th->getMessage();
            }
        }

        if (!isset($error)) {
            return response()->json(['message' => 'File successfully uploaded', 'filenames' => $files], 200);
        }

        return response()->json(['message' => $error, 'filename' => false], 500);
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
        $filePath = $this->temp_path.$moduleName.'/'.$filename;
        $filenameToStore=$this->getFilenameToStore($filename);
        $fileToStore = $this->file_path.$moduleName.'/'. $filenameToStore;

        try {
            move_uploaded_file($filePath, $fileToStore);
            dump($filePath, $fileToStore);
            $messages['filename'] = $filenameToStore;
        } catch (\Exception $ex) {
            $messages['errors'][] = ['fileError' => $ex->getMessage()];
        }
        return $messages;
    }

    /**
     * @return string
     */
    public function processImage($filename, $moduleName)
    {
        $messages = ['errors'=>[]];
        $filePath = $this->temp_path.$moduleName.'/'.$filename;
        $filenameToStore=$this->getFilenameToStore($filename);
        $img = Image::make($filePath);
        $imgSize = $img->filesize();
        
        foreach ($this->thumbnail_sizes as $thumbType => $thumbSizes)
        {
            $thumbDir          = $thumbType;
            [$width, $height]  = explode('x', $thumbSizes);
            
            $dir = $this->image_path.$moduleName.'/'.$thumbDir;

            $img->resize($width, $height, function($constraint) {
                $constraint->aspectRatio();
            });

            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }

            $thumbnailpath = $dir.'/'.$filenameToStore;
            try {
                $img->save($thumbnailpath);
            } catch (\Exception $ex) {
                $messages['errors'][] = ['fileError' => $ex->getMessage()];
            }
        }
        $messages['filename'] = $filenameToStore;
        $messages['size'] = $imgSize;
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
        // Needs revision
        return true;
    }
}
