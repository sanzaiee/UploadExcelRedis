<?php

namespace App\Import;

use App\Models\MobileNumber;
use App\Service\ImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MobileNumbersImport extends ImportService
{
    private const EXPECTED_COLUMNS = [
        'Mobile Number',
    ];

        /**
     * Create a new class instance.
     */
    public function __construct($filePath)
    {
        parent::__construct($filePath);

        $this->validateColumns(self::EXPECTED_COLUMNS);
    }


    public function formattedData(array $row): array
    {
        return [
            'mobile_number' => $row['Mobile Number'] ?? null,
        ];
    }

    private function validateData($data)
    {
        $rules = ['mobile_number' => ['required', 'regex:/^(\+?\d{1,3})?[\s.-]?\d{10}$/']];

        $validator = Validator::make($data,$rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            return true;
        }
    }

    protected function getRowsInChunks(int $chunkSize)
    {
        $rows = $this->getRows();
        return array_chunk($rows,$chunkSize);
    }
    public function import()
    {
        $invalidNumbers = [];

        foreach($this->getRowsInChunks(5000) as $chunk)
        {
            DB::beginTransaction();
            try{
                  foreach($chunk as $index => $row){
                        $data = $this->formattedData($row);
                        $validated = $this->validateData($data);
                        if($validated === true){
                            MobileNumber::updateOrCreate(['mobile_number' => $data['mobile_number']]);
                        }else{
                            $invalidNumbers[] = [
                                'row' => $index + 1,
                                'mobile_number' => $data['mobile_number'],
                                'errors' => $validated
                            ];
                        }
                  }

                DB::commit();

            }catch (\Exception $e){
                DB::rollBack();

                Log::error('Error processing row',[
                    'row' => $row,
                    'error' => $e->getMessage()
                ]);

                activity()->event('Failed Processing Row')
                        ->log('Failed to process row while chunking : ' . json_encode($row). 'Errors:' . $e->getMessage());
            }
        }

        if(!empty($invalidNumbers)){

            Log::error('Invalid mobile number detected. Please check Excel file',[
                'invalid_numbers' => $invalidNumbers,
            ]);

            activity()->event('Invalid Mobile Numbers')
                    ->withProperties(['invalid_numbers' => $invalidNumbers])
                    ->log('Invalid numbers detected. Please check excel file.');
        }else{
              activity()->event('Uploaded Mobile Numbers')
                    ->log('Successfully updated all mobile numbers.');
        }

        // $this->unLinkFile(); //only for manual testing

    }

}
