<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

abstract class ImportService
{
    protected string $filePath;
    /**
     * Create a new class instance.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function getRows(): array
    {
        try {
            return SimpleExcelReader::create($this->filePath)
                ->getRows()
                ->toArray();
        }catch(\Exception $e)
        {
            Log::error('Failed to read rows from Excel file',['error' => $e->getMessage()]);
            throw new \RuntimeException(('Failed to read rows from the uploaded file'));
        }
    }

    public function getHeaders(): array
    {
        try {
            return SimpleExcelReader::create($this->filePath)
                ->getHeaders();
        }catch(\Exception $e)
        {
            Log::error('Failed to read header from Excel file',['error' => $e->getMessage()]);
            throw new \RuntimeException(('Failed to read header from the uploaded file'));
        }

    }

    protected function validateColumns(array $expectedColumns): void
    {
        try {
            $missingColumns = array_diff($expectedColumns, $this->getHeaders());
            $extraColumns = array_diff($expectedColumns, $this->getHeaders());

            if(!empty($missingColumns) || !empty($extraColumns)){
                  activity()->event('Invalid columns in the excel file')
                    ->withProperties(['expected_columns' => $expectedColumns,'missing_columns' =>  $missingColumns])
                    ->log('Column Validation Failed');


                Log::error('Invalid columns in the excel file.');
                throw new \RuntimeException('Invalid columns in the excel file. Missing columns. ' . json_encode($missingColumns));
            }

        }catch (\Exception $e){

            activity()->event('Column Validation Failed')
                ->withProperties(['expected_columns' => $expectedColumns,'error' =>  $e->getMessage()])
                ->log('Column Validation Failed');

            Log::error('Column Validation Failed',[
                'error' => $e->getMessage(),
                'expected' => $expectedColumns
            ]);
            throw new \RuntimeException("The excel format is incorrect. Expected columns. " . json_encode($expectedColumns));
        }
    }

    abstract public function formattedData(array $row): array;

    public function unLinkFile(): void
    {
        $path = storage_path('app/private/temp/imported.xlsx');
        try{
            if(file_exists($path)){
                unlink($path);
                activity()->event('Temp File Deleted')
                    ->log('Temporary file has been deleted');
            }
        }
        catch(\Exception $e)
        {
            activity()->event('Failed delete temporary file')
                    ->withProperties(['error' =>  $e->getMessage()])
                    ->log('Temporary file has been deleted');
            Log::error('Failed delete temporary file',['error' => $e->getMessage()]);
        }
    }
}
