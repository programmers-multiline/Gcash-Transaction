<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Uploads;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Models\TransactionInfo;
use App\Imports\TransactionImport;
use App\Models\TransactionUploads;
use App\Models\GoogleFormResponses;
use Illuminate\Support\Facades\Auth;
use App\Imports\AccTransactionImport;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;

class FileUploadController extends Controller
{
    public function upload_transaction(Request $request)
    {


        if ($request->hasFile('importTransaction')) {

            $transaction = $request->file('importTransaction');

            $fileExtension = $transaction->getClientOriginalExtension();

            if ($fileExtension === 'xlsx') {
                $rows = ExcelFacade::toArray(new TransactionImport, $transaction, Excel::XLSX);
            } elseif ($fileExtension === 'xls') {
                $rows = ExcelFacade::toArray(new TransactionImport, $transaction, Excel::XLS);
            } else {
            }

            $transactions = GoogleFormResponses::where('status', 1)->get();

            $client_ids = collect($transactions)->pluck('client_id')->toArray();

            foreach ($rows[0] as $i => $row) {
                if ($i > 0) {
                    $have_client_id = in_array($row[0], $client_ids);
                    if (!$have_client_id) {
                        return $row[1];
                    }
                }
            }



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


            $transaction = TransactionInfo::where('status', 1)->orderBy('transaction_number', 'desc')->first();


            foreach ($rows[0] as $i => $row) {
                if ($i > 0) {
                    Transactions::create([
                        'transaction_id'     => $transaction->id,
                        'transaction_number' => $transaction->transaction_number,
                        'client_id'          => $row[0],
                        'client_name'        => $row[1],
                        'amount'             => $row[2],
                        'amount_deducted'    => $row[2] > 5 ? $row[2] - 5 : 0,
                        'remarks'            => $row[3],
                        'user_id'            => Auth::user()->id,
                    ]);
                }
            }
        }
    }

    public function upload_transaction_acc(Request $request)
    {

        if ($request->hasFile('importTransaction')) {

            $transaction = $request->file('importTransaction');

            // $transaction_name = mt_rand(111111, 999999) . date('YmdHms') . '.' . $transaction->extension();
            // $request->file('importTransaction')->storeAs('uploads/transactions', $transaction_name);

            $fileExtension = $transaction->getClientOriginalExtension();

            if ($fileExtension === 'xlsx') {
                $excel = ExcelFacade::toArray(new AccTransactionImport, $transaction, Excel::XLSX);
            } elseif ($fileExtension === 'xls') {
                $excel = ExcelFacade::toArray(new AccTransactionImport, $transaction, Excel::XLS);
            } else {
                $excel = ExcelFacade::toArray(new AccTransactionImport, $transaction);
            }

            return $excel;
        }
    }
}
