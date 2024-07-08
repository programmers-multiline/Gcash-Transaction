<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Uploads;
use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\TransactionInfo;
use App\Imports\TransactionImport;
use App\Models\TransactionUploads;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Maatwebsite\Excel\Excel;

class FileUploadController extends Controller
{
    public function upload_transaction(Request $request)
    {

        $prev_tn = TransactionInfo::where('status', 1)->orderBy('transaction_number', 'desc')->first();

        if (!$prev_tn) {
            $new_transaction_number = 1000;
        } else {
            $new_transaction_number = $prev_tn->transaction_number + 1;
        }

        TransactionInfo::create([
            'transaction_number' => $new_transaction_number,
            'date_uploaded' => Carbon::now(),
            'created_by' => Auth::user()->id,
        ]);

        if ($request->hasFile('importTransaction')) {

            $transaction = $request->file('importTransaction');


            // dd();
            // $transaction_name = mt_rand(111111, 999999) . date('YmdHms') . '.' . $transaction->extension();
            $transaction_name = mt_rand(111111, 999999) . date('YmdHms') . '.' . $transaction->extension();
            Uploads::create([
                'name' => $transaction_name,
                'original_name' => $transaction->getClientOriginalName(),
                'extension' => $transaction->extension(),
            ]);
            $request->file('importTransaction')->storeAs('uploads/transactions', $transaction_name);



            $uploads = Uploads::where('status', 1)->orderBy('id', 'desc')->first();

            TransactionUploads::create([
                'upload_id' => $uploads->id,
                'user_id' => Auth::user()->id,
            ]);


            $fileExtension = $transaction->getClientOriginalExtension();

            if ($fileExtension === 'xlsx') {
                ExcelFacade::import(new TransactionImport, $transaction, Excel::XLSX);
            } elseif ($fileExtension === 'xls') {
                ExcelFacade::import(new TransactionImport, $transaction, Excel::XLS);
            } else {

            }
        }
    }
}
