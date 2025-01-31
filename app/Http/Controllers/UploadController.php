<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Import\MobileNumbersImport;
use App\Jobs\ProcessMobileNumbers;
use App\Models\MobileNumber;
use App\Traits\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class UploadController extends Controller
{
    use UploadFile;

    public function upload(UploadRequest $request){
        try {
            $filePath = $this->storeFile($request->file);

            ProcessMobileNumbers::dispatch($filePath);

            activity()->event('File Uploaded')->log('File Uploaded and proceeded successfully');

            return back()->with('success','File Uploaded and proceed to importing..');

        } catch (\Exception $e) {
            activity()->event('Error')->log('Failed to upload: '. $e->getMessage());

            return back()->with('danger', $e->getMessage());
        }
    }

    public function index()
    {
        $numbers = MobileNumber::latest()->paginate(20);
        return view('upload.index',compact('numbers'));
    }

    public function activityLog()
    {
        $logs = Activity::latest()->paginate(10);
        return view('upload.logs',compact('logs'));
    }

    public function mobileNumbersDestroy()
    {
        DB::table('mobile_numbers')->delete();
        return back()->with('success','Deleted activity log successfully');
    }
    public function activityLogDestroy()
    {
        DB::table('activity_log')->delete();
        return back()->with('success','Deleted activity log successfully');
    }
}
