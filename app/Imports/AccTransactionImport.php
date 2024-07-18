<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class AccTransactionImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        // ExcelFacade::import(new TransactionImport($transaction, $sampleVar), Excel::XLSX); - ganyan maglagay pag mag pasa pa ng dalawang var maliban sa excel file
        // $transaction = TransactionInfo::where('status', 1)->orderBy('transaction_number', 'desc')->first();
        

        // $counter = 0;
        // foreach ($rows as $row) {
        //     if($counter > 0){
        //         Transactions::create([
        //             'transaction_id' => $transaction->id,
        //             'transaction_number' => $transaction->transaction_number,
        //             'mobile_number' => $row[0],
        //             'client_name' => $row[1],
        //             'amount' => $row[2],
        //             'remarks' => $row[3],
        //             'user_id' => Auth::user()->id,
        //         ]);
        //     }
        //     $counter++;
        // }
    }
}
