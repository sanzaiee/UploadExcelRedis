<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait UploadFile
{
    private function storeFile($file): string
    {
        $this->validateFile($file);
        try {
            $fileUpload = Storage::putFileAs(
                'temp',$file,'imported.xlsx'
            );

            return Storage::path($fileUpload);
        }catch (\Exception $e){
            Log::error('Failed to store file', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Fail to store the uploaded file');
        }
    }
    private function validateFile($file): void
    {
        $allowedExtension = ['xlsx','xls'];
        if(!$file->isValid() && !in_array($file->getClientOriginalExtenstion(),$allowedExtension))
        {
            throw new \InvalidArgumentException('Invalid File. Please upload a valid excel with .xlsx extension');
        }
    }

}
