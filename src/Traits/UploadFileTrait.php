<?php


namespace FaceDigital\Crudify\Traits;


use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait UploadFileTrait
{
    /**
     * @var
     */
    protected $basePath = 'arquivos';

    /**
     * @var
     */
    protected $driver = 's3';

    /**
     * @var
     */
    protected $baseIdentify;

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string $path
     */
    public function setBasePath($path)
    {
        $this->basePath = $path;
    }

    /**
     * @return string
     */
    public function getUploadIdentify()
    {
        return $this->baseIdentify;
    }

    /**
     * @param string $baseIdentify
     */
    public function setUploadIdentify($baseIdentify)
    {
        $this->baseIdentify = $baseIdentify;
        return $this;
    }

    /**
     * @return string
     */
    public function getUploadDriver()
    {
        return $this->driver;
    }

    /**
     * @param string $driver
     */
    public function setUploadDriver($driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(){
        $base = $this->getBasePath();
        if($this->getUploadIdentify()){
            $base .= $this->getUploadIdentify().'/';
        }
        return $base;
    }

    /**
     * @param UploadedFile $file
     * @param bool $uploadName
     * @param string $custonName
     * @return string
     */
    public function uploadName(UploadedFile $file,bool $uploadName, string $customName){
        $name = Str::uuid();
        if ($uploadName) {
            $name = rtrim(Str::slug($file->getClientOriginalName()),'.'.$file->getClientOriginalExtension()).'.'.mb_strtolower($file->getClientOriginalExtension());
        } elseif (!is_null($customName) && !empty(trim($customName))) {
            $name = Str::slug($customName).'.'.mb_strtolower($file->getClientOriginalExtension());
        } else {
            $name .= '.'.mb_strtolower($file->getClientOriginalExtension());
        }
        return $name;
    }

    /**
     * @param Request $request
     * @param string $fieldname
     * @param bool $uploadName
     * @return string|null
     */
    public function verifyAndUpload(Request $request, string $fieldname,bool $uploadName = false) {
        if ($request->hasFile($fieldname)) {
            $file = $request->file($fieldname);
            #todo
            /* Multiplos itens */
            return $this->uploadFile($file,$uploadName);
        }
        return null;
    }

    /**
     * @param UploadedFile $file
     * @param bool $uploadName
     * @param string $custonName
     * @return string|null
     */
    public function uploadFile(UploadedFile $file,bool $uploadName = false, string $customName = ''){
        if (!$file->isValid()) {
            return null;
        }
        $uploadMethod = 'uploadBy'.$this->getUploadDriver();
        if(method_exists($this, $uploadMethod)){
            $response = $this->$uploadMethod($file, $uploadName, $customName);
        } else {
            $response = $this->uploadByLocal($file, $uploadName, $customName);
        }
        return $response;
    }

    public function uploadByS3 (UploadedFile $file, bool $uploadName = false, string $custonName = '')
    {
        $pathToUpload = $this->getPath();
        $uploadName = $this->uploadName($file, $uploadName, $custonName);
        return $file->storeAs($pathToUpload, $uploadName, 's3');

    }

    // public function uploadByS3 (UploadedFile $file, bool $uploadName = false, string $custonName = '')
    // {
    //     $pathToUpload = $this->getPath();
    //     $uploadName = $this->uploadName($file, $uploadName, $custonName);
    //     // if($file->storeAs($pathToUpload, $uploadName, 's3')){
    //     if (Storage::disk('s3')->put('avatars/1', $fileContents))
    //     {
    //         return $uploadName;
    //     }
    // }

    public function uploadByLocal (UploadedFile $file,bool $uploadName = false, string $custonName = ''){

        $pathToUpload = $this->getPath();
        $uploadName = $this->uploadName($file, $uploadName, $custonName);
        if($file->storeAs($pathToUpload, $uploadName)){
            return $uploadName;
        }
        return null;
    }

    public function deleteFile($file){
        return File::delete($file);
    }

}
