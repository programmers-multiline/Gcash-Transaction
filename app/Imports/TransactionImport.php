<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Transactions;
use App\Models\TransactionInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class TransactionImport implements ToCollection
{
    /**
    * @param Collection $collection
    */

    public function collection(Collection $rows)
    {
        $transaction = TransactionInfo::where('status', 1)->orderBy('transaction_number', 'desc')->first();
        

        $counter = 0;
        foreach ($rows as $row) {
            if($counter > 0){
                Transactions::create([
                    'transaction_id' => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                    'client_id' => $row[0],
                    'client_name' => $row[1],
                    'amount' => $row[2],
                    'amount_deducted' => $row[2] > 5 ? $row[2] - 5 : 0,
                    'remarks' => $row[3],
                    'user_id' => Auth::user()->id,
                ]);
            }
            $counter++;
        }
    }
}
