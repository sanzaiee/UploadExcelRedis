<?php

namespace App\Jobs;

use App\Import\MobileNumbersImport;
use App\Models\MobileNumber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessMobileNumbers implements ShouldQueue
{
    use Queueable;

    public $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $import = new MobileNumbersImport($this->filePath);
        $import->import();


        // foreach($this->mobileNumbers as $mobileNumber){
        //     MobileNumber::updateOrCreate([
        //         'mobile_number' => $mobileNumber
        //     ]);
        // }
    }

    public function failed(\Exception $e)
    {
        activity()
        ->event('Job Failed')
        ->withProperties(['error' => $e->getMessage()])
        ->log('Mobile number processing job failed');
    }
}
