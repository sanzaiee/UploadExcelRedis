<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MobileNumber extends Model
{
    use LogsActivity;

    protected $fillable = ['mobile_number'];

   public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function failed(\Exception $exception)
    {
        Log::error("Job Failed: " . $exception->getMessage());
          activity()->event('Job Failed')
                        ->log('Failed Job Errors:' . $exception->getMessage());

    }
}
