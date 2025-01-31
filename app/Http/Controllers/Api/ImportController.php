<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadRequest;
use App\Jobs\ProcessMobileNumbers;
use App\Traits\UploadFile;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    use UploadFile;

    public function upload(UploadRequest $request){
        try {
            $filePath = $this->storeFile($request->file);

            ProcessMobileNumbers::dispatch($filePath);

            activity()->event('File Uploaded')->log('File Uploaded and proceeded successfully');

            return response()->json(['status' => true,'message' => 'File Uploaded and proceed to importing..'],200);

        } catch (\Exception $e) {
            activity()->event('Error')->log('Failed to upload: '. $e->getMessage());

            return response()->json(['status' => false, 'message' => 'Failed to upload. Please check file type and content'],200);
        }
    }
}
