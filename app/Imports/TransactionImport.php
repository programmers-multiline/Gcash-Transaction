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
        
    }
}
