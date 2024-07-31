<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\NotifyTreasury;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Models\TransactionInfo;
use Yajra\DataTables\DataTables;
use App\Models\GoogleFormResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Imports\AccTransactionImport;
use App\Models\AccTransactionListUploads;
use App\Imports\ApproverTransactionImport;
use App\Models\ApproverTransactionListUploads;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;

class TransactionController extends Controller
{
    public function fetch_transactions(Request $request)
    {

        $transactions = TransactionInfo::leftjoin('users', 'users.id', 'transaction_infos.created_by')
            ->select('transaction_infos.*', 'users.firstname as fn', 'users.lastname as ln')
            ->where('transaction_infos.status', 1)
            ->where('users.status', 1);

        if ($request->path == "pages/transactions_acc" || $request->path == "pages/transactions") {
            $transactions = $transactions->where('transaction_infos.progress', 'pending');
        } elseif ($request->path == "pages/transaction_logs" || $request->path == "pages/transaction_treasury" || $request->path == "pages/transaction_approver") {
            $transactions = $transactions->where('transaction_infos.progress', 'done');
            if (Auth::user()->user_type_id == 5) {
                $transactions = $transactions->where('transaction_infos.approver_status', 1);
            }
        }

        $transactions = $transactions->get();

        return DataTables::of($transactions)
            ->addColumn('view_transaction_lists', function ($row) {

                return '<button data-tn="' . $row->transaction_number . '" data-bs-toggle="modal" data-bs-target="#transactionListModal" class="viewTransaction btn text-primary fs-6"><i class="fa fa-eye me-1"></i>View</button>';;
            })

            ->addColumn('total_number_approved', function ($row) {

                $total_approved = Transactions::where('status', 1)
                    ->where('transaction_status', 'approved')
                    ->where('transaction_number', $row->transaction_number)
                    ->count();

                return '<button data-status="approved" data-tn="' . $row->transaction_number . '" data-bs-toggle="modal" data-bs-target="#transactionListModal" class="viewTransaction btn text-primary fs-6 d-block me-auto">' . $total_approved . '</button>';;
            })
            ->addColumn('total_number_declined', function ($row) {

                $total_declined = Transactions::where('status', 1)
                    ->where('transaction_status', 'declined')
                    ->where('transaction_number', $row->transaction_number)
                    ->count();

                return '<button data-status="declined" data-tn="' . $row->transaction_number . '" data-bs-toggle="modal" data-bs-target="#transactionListModal" class="viewTransaction btn text-primary fs-6 d-block me-auto">' . $total_declined . '</button>';;
            })

            ->addColumn('status', function ($row) {

                $progress = $row->progress;
                if ($progress == 'done') {
                    $progress = '<span class="badge bg-success">' . $progress . '</span>';
                } else {
                    $progress =  '<span class="badge bg-warning">' . $progress . '</span>';
                }

                return $progress;
            })

            ->addColumn('action', function ($row) {

                if (Auth::user()->user_type_id == 3) {
                    $action =  '';
                    // $action =  '<div class="d-flex gap-2 justify-content-center">
                    // <button data-id="'.$row->id.'" type="button" class="approveBtn btn btn-sm btn-primary js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approve?" data-bs-original-title="Approve?"><i class="fa fa-check"></i></button>
                    // <button data-id="'.$row->id.'" type="button" class="declineBtn btn btn-sm btn-danger js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Decline?" data-bs-original-title="Decline?"><i class="fa fa-xmark"></i></button>
                    // </div>';
                } else {
                    $action =  '';
                }

                return $action;
            })
            ->rawColumns(['status', 'view_transaction_lists', 'total_number_approved', 'total_number_declined', 'action'])
            ->toJson();
    }

    public function fetch_transaction_modal(Request $request)
    {

        if ($request->status) {
            $transactions = Transactions::leftjoin('transaction_infos', 'transaction_infos.id', 'transactions.transaction_id')
                ->leftjoin('google_form_responses','google_form_responses.client_id','transactions.client_id')
                ->select('transactions.*', 'google_form_responses.gcash_number', 'google_form_responses.branch_name')
                ->where('transaction_infos.transaction_number', $request->transacNum)
                ->where('transactions.transaction_status', $request->status)
                ->where('transaction_infos.status', 1)
                ->where('transactions.status', 1)
                ->get();
        } else {
            $transactions = Transactions::leftjoin('transaction_infos', 'transaction_infos.id', 'transactions.transaction_id')
                ->leftjoin('google_form_responses','google_form_responses.client_id','transactions.client_id')
                ->select('transactions.*', 'google_form_responses.gcash_number', 'google_form_responses.branch_name')
                ->where('transaction_infos.transaction_number', $request->transacNum)
                ->where('transaction_infos.status', 1)
                ->where('transactions.status', 1)
                ->get();
        }

        return DataTables::of($transactions)
            ->addColumn('status', function ($row) {

                $status = $row->transaction_status;
                if (Auth::user()->user_type_id == 2) {

                    if ($status == 'approved') {
                        $status = '<span class="badge bg-success">' . $status . '</span>';
                    } else if (!$row->gcash_number) {
                        $status =  '<span class="badge bg-elegance">no client id</span>';
                    } else if ($status == 'pending') {
                        $status =  '<span class="badge bg-warning">' . $status . '</span>';
                    } else {
                        $status =  '<span class="badge bg-danger">' . $status . '</span>';
                    }
                }

                if (Auth::user()->user_type_id == 3) {
                    if ($status == 'approved') {
                        $status = '<div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success">' . $status . '</span>
                    <button data-id="' . $row->id . '" class="undoStatus btn text-elegance fs-6 d-block"><i class="fa fa-arrow-rotate-left"></i></button>
                    </div>';
                    } else if (!$row->gcash_number) {
                        $status =  '<div class="d-flex align-items-center gap-2">
                        <span class="badge bg-elegance">no client id</span>
                        </div>';
                    }  else if ($status == 'pending') {
                        $status = '<div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning">' . $status . '</span>
                        <button data-id="' . $row->id . '" class="undoStatus btn text-elegance fs-6 d-block"><i class="fa fa-arrow-rotate-left"></i></button>
                        </div>';
                    } else {
                        $status = '<div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger">' . $status . '</span>
                        <button data-id="' . $row->id . '" class="undoStatus btn text-elegance fs-6 d-block"><i class="fa fa-arrow-rotate-left"></i></button>
                        </div>';
                    }
                }


                if (Auth::user()->user_type_id == 5) {
                    $status = $row->approver_status;
                    if ($status == 1) {
                        $status = '<span class="badge bg-success">approved</span>';
                    } else if ($status == 2) {
                        $status =  '<span class="badge bg-danger">declined</span>';
                    } else {
                        $status =  '<span class="badge bg-warning">declined by accounting</span>';
                    }
                }

                return $status;
            })

            ->addColumn('mobile_number', function ($row) {

                return $row->gcash_number;

                // $transactions = Transactions::select('mobile_number', 'transaction_number')
                //     ->groupBy(['mobile_number', 'transaction_number'])
                //     ->havingRaw('COUNT(*) > 1')
                //     ->get();

                // $duplicate_mn = $transactions->pluck('mobile_number')->toArray();

                // $is_duplicate = in_array($row->mobile_number, $duplicate_mn);

                // if ($is_duplicate) {
                //     return '<div class="ribbon ribbon-left ribbon-danger" style="min-height: unset;">
                //                 <div class="ribbon-box" style="margin-top: -30px; margin-bottom: 5px; padding: .5px; height: 17px;"><p style="font-size: 10px;">Duplicate</p></div>
                //                     <p class="mb-0 mt-2">' . $row->mobile_number . '</p>
                //             </div>';
                // } else {
                //     return $row->mobile_number;
                // }
            })

            ->addColumn('total_number_approved', function ($row) {

                $total_approved = Transactions::where('status', 1)
                    ->where('transaction_status', 'approved')
                    ->where('transaction_number', $row->transaction_number)
                    ->count();

                return '<button data-status="approved" data-tn="' . $row->transaction_number . '" data-bs-toggle="modal" data-bs-target="#transactionListsModal" class="viewTransaction btn text-primary fs-6 d-block me-auto">' . $total_approved . '</button>';;
            })
            ->addColumn('total_number_declined', function ($row) {

                $total_declined = Transactions::where('status', 1)
                    ->where('transaction_status', 'declined')
                    ->where('transaction_number', $row->transaction_number)
                    ->count();

                return '<button data-status="declined" data-tn="' . $row->transaction_number . '" data-bs-toggle="modal" data-bs-target="#transactionListsModal" class="viewTransaction btn text-primary fs-6 d-block me-auto">' . $total_declined . '</button>';;
            })

            ->setRowClass(function ($row) {

                $no_client_id = $row->gcash_number ? '' : 'bg-gray';

                return $no_client_id;
            })

            ->addColumn('amount', function ($row) {
                return number_format($row->amount - 5, 2);
            })

            ->addColumn('action', function ($row){
                      return '<button type="button" id="editTransactionBtn" data-id="'.$row->id.'" data-branch="'.$row->branch_name.'" data-mn="'.$row->gcash_number.'" data-cn="'.$row->client_name.'" data-amount="'.$row->amount.'" data-remarks="'.$row->remarks.'" class="btn btn-sm btn-info js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                <i class="fa fa-pencil-alt"></i>
              </button>
              <button type="button" id="deleteTransactionsBtn" data-id="'.$row->id.'" class="btn btn-sm btn-danger js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete">
                <i class="fa fa-times"></i>
              </button>';
            })

            ->rawColumns(['status', 'total_number_approved', 'total_number_declined', 'mobile_number', 'action'])
            ->toJson();
    }

    public function edit_transactions(Request $request){
        $transaction = Transactions::find($request->id);

        $gfr = GoogleFormResponses::where('status', 1)->where('client_id', $transaction->client_id)->first();
        
        $transaction->client_name = $request->cn;
        $transaction->amount = $request->amount;
        $transaction->remarks = $request->remarks;
        $gfr->gcash_number = $request->mn;

        $transaction->update();
        $gfr->update();
    }

    public function delete_transaction(Request $request){
        $transaction = Transactions::find($request->id);

        $transaction->status = 0;

        $transaction->update();
    }


    public function fetch_transactions_approved(Request $request)
    {
        $transactions_approved = TransactionInfo::leftjoin('transactions', 'transactions.transaction_id', 'transaction_infos.id')
            ->leftjoin('google_form_responses as gfr','gfr.client_id','transactions.client_id')
            ->select('transactions.*', 'gfr.gcash_number', 'gfr.gcash_name', 'gfr.branch_name')
            ->where('transactions.status', 1)
            ->where('gfr.status', 1)
            ->where('transaction_infos.status', 1)
            ->where('transaction_status', 'approved')
            ->where('transactions.transaction_number', $request->transacNum);

        if ($request->status) {
            $transactions_approved = TransactionInfo::leftjoin('transactions', 'transactions.transaction_id', 'transaction_infos.id')
                ->leftjoin('google_form_responses as gfr','gfr.client_id','transactions.client_id')
                ->select('transactions.*', 'gfr.gcash_number', 'gfr.gcash_name', 'gfr.branch_name')
                ->where('transactions.status', 1)
                ->where('gfr.status', 1)
                ->where('transaction_infos.status', 1)
                ->where('transaction_status', $request->status)
                ->where('transactions.transaction_number', $request->transacNum);
        }

        if (Auth::user()->user_type_id == 5) {
            $transactions_approved = Transactions::where('status', 1)
                ->where('approver_status', 1)
                ->get();
        }

        return DataTables::of($transactions_approved)
            ->addColumn('status', function ($row) {

                $status = $row->transaction_status;
                if ($status == 'approved') {
                    $status = '<span class="badge bg-success">' . $status . '</span>';
                } else if ($status == 'pending') {
                    $status =  '<span class="badge bg-warning">' . $status . '</span>';
                } else {
                    $status =  '<span class="badge bg-danger">' . $status . '</span>';
                }

                return $status;
            })

            ->addColumn('amount', function ($row) {
                return number_format($row->amount - 5, 2);
            })

            ->rawColumns(['status'])
            ->toJson();
    }





    // no use
    public function fetch_transactions_declined()
    {
        $transactions_declined = Transactions::where('status', 1)
            ->where('transaction_status', 'declined')
            ->get();

        if (Auth::user()->user_type_id == 5) {
            $transactions_declined = Transactions::where('status', 1)
                ->where('approver_status', 0)
                ->get();
        }

        return DataTables::of($transactions_declined)->toJson();
    }








    public function approve_transaction(Request $request)
    {

        $ids = json_decode($request->idArray);

        foreach ($ids as $id) {
            $transaction = Transactions::find($id);
            if ($request->path == "pages/transaction_approver") {
                $transaction->approver_status = 1;
                $transaction->approved_by = Auth::user()->id;
                $transaction->approver_date_approved_declined = Carbon::now();
            } else {
                $transaction->transaction_status = 'approved';
                $transaction->acc_approver = Auth::user()->id;
                $transaction->acc_date_approved_declined = Carbon::now();
            }

            $transaction->update();
        }



        if ($request->path == "pages/transaction_approver") {
            $transac = Transactions::where('transactions.status', 1)
                ->where('transactions.transaction_id', $transaction->transaction_id)
                ->where('transaction_status', '!=', 'declined')
                ->get();

            $transaction_status = collect($transac)->pluck('approver_status')->toArray();


            $in_array = in_array(null, $transaction_status);


            if (!$in_array) {
                $ti = TransactionInfo::find($transaction->transaction_id);
                $ti->approver_status = 1;
                $ti->update();
            }

            return $transaction_status;
        } else {
            $transac = Transactions::where('transactions.status', 1)
                ->where('transactions.transaction_id', $transaction->transaction_id)
                ->get();

            $transaction_status = collect($transac)->pluck('transaction_status')->toArray();


            $in_array = in_array('pending', $transaction_status) || in_array('declined', $transaction_status);

            if (!$in_array) {
                $ti = TransactionInfo::find($transaction->transaction_id);
                $ti->progress = "done";
                $ti->update();

                $user = User::where('status', 1)->where('user_type_id', 4)->first();
    
                $mail_data = ['user' => $user->firstname . ' ' . $user->lastname, 'transaction_number' => $transac[0]->transaction_number, 'date_uploaded' => $ti->date_uploaded];
    
                Mail::to($user->email)->send(new NotifyTreasury($mail_data));
            }


            return $transac;
        }
    }

    public function decline_transaction(Request $request)
    {

        $ids = json_decode($request->idArray);

        foreach ($ids as $id) {
            $transaction = Transactions::find($id);
            if ($request->path == "pages/transaction_approver") {
                $transaction->approver_status = 2;
                $transaction->approved_by = Auth::user()->id;
                $transaction->approver_date_approved_declined = Carbon::now();
            } else {
                $transaction->transaction_status = 'declined';
                $transaction->acc_approver = Auth::user()->id;
                $transaction->acc_date_approved_declined = Carbon::now();
            }

            $transaction->update();
        }

        if ($request->path == "pages/transaction_approver") {
            $transac = Transactions::where('transactions.status', 1)
                ->where('transactions.transaction_id', $transaction->transaction_id)
                ->where('transaction_status', '!=', 'declined')
                ->get();

            $transaction_status = collect($transac)->pluck('approver_status')->toArray();


            $in_array = in_array(null, $transaction_status);

            if (!$in_array) {
                $ti = TransactionInfo::find($transaction->transaction_id);
                $ti->approver_status = 1;
                $ti->update();
            }

            return $transaction_status;
        } else {
            $transac = Transactions::where('transactions.status', 1)
                ->where('transactions.transaction_id', $transaction->transaction_id)
                ->get();

            $transaction_status = collect($transac)->pluck('transaction_status')->toArray();


            $in_array = in_array('pending', $transaction_status) || in_array('declined', $transaction_status);

            if (!$in_array) {
                $ti = TransactionInfo::find($transaction->transaction_id);
                $ti->progress = "done";
                $ti->update();
            }

            return $transac;
        }
    }


    public function fetch_transactions_approver()
    {
        $transactions = TransactionInfo::leftjoin('users', 'users.id', 'transaction_infos.created_by')
            ->select('transaction_infos.*', 'users.firstname as fn', 'users.lastname as ln')
            ->where('transaction_infos.status', 1)
            ->where('transaction_infos.progress', 'done')
            ->whereNull('transaction_infos.approver_status')
            ->where('users.status', 1)
            ->get();

        return DataTables::of($transactions)
            ->addColumn('view_transaction_lists', function ($row) {

                return '<button data-tn="' . $row->transaction_number . '" data-bs-toggle="modal" data-bs-target="#transactionListModal" class="viewTransaction btn text-primary fs-6"><i class="fa fa-eye me-1"></i>View</button>';;
            })

            ->addColumn('total_number_approved', function ($row) {

                $total_approved = Transactions::where('status', 1)
                    ->where('approver_status', 1)
                    ->where('transaction_number', $row->transaction_number)
                    ->count();

                return '<button data-status="1" data-tn="' . $row->transaction_number . '" data-bs-toggle="modal" data-bs-target="#transactionListModal" class="viewTransaction btn text-primary fs-6 d-block me-auto">' . $total_approved . '</button>';;
            })
            ->addColumn('total_number_declined', function ($row) {

                $total_declined = Transactions::where('status', 1)
                    ->where('approver_status', 2)
                    ->where('transaction_number', $row->transaction_number)
                    ->count();

                return '<button data-status="2" data-tn="' . $row->transaction_number . '" data-bs-toggle="modal" data-bs-target="#transactionListModal" class="viewTransaction btn text-primary fs-6 d-block me-auto">' . $total_declined . '</button>';;
            })

            ->addColumn('status', function ($row) {

                $approver_status = $row->approver_status;
                if ($approver_status == 1) {
                    $approver_status = '<span class="badge bg-success">done</span>';
                } else {
                    $approver_status =  '<span class="badge bg-warning">pending</span>';
                }

                return $approver_status;
            })

            ->addColumn('action', function ($row) {

                if (Auth::user()->user_type_id == 3) {
                    $action =  '';
                    // $action =  '<div class="d-flex gap-2 justify-content-center">
                    // <button data-id="'.$row->id.'" type="button" class="approveBtn btn btn-sm btn-primary js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Approve?" data-bs-original-title="Approve?"><i class="fa fa-check"></i></button>
                    // <button data-id="'.$row->id.'" type="button" class="declineBtn btn btn-sm btn-danger js-bs-tooltip-enabled" data-bs-toggle="tooltip" aria-label="Decline?" data-bs-original-title="Decline?"><i class="fa fa-xmark"></i></button>
                    // </div>';
                } else {
                    $action =  '';
                }

                return $action;
            })
            ->rawColumns(['status', 'view_transaction_lists', 'total_number_approved', 'total_number_declined', 'action'])
            ->toJson();
    }




    public function fetch_transactions_approver_modal(Request $request)
    {
        if ($request->status) {
            $transactions = Transactions::where('status', 1)
                ->where('transaction_number', $request->transacNum)
                ->where('approver_status', $request->status)
                ->orderBy('transaction_status', 'asc')
                ->get();
        } else {
            $transactions = Transactions::where('status', 1)
                ->where('transaction_number', $request->transacNum)
                // ->whereNull('approver_status')
                ->orderBy('transaction_status', 'asc')
                ->get();
        }

        return DataTables::of($transactions)

            ->addColumn('acc_mn', function ($row) {


                if ($row->transaction_status == 'approved') {
                    return $row->mobile_number;
                } else {
                    return '';
                }
            })

            ->addColumn('acc_cn', function ($row) {


                if ($row->transaction_status == 'approved') {
                    return $row->client_name;
                } else {
                    return '';
                }
            })

            ->addColumn('status', function ($row) {

                $status = $row->transaction_status;
                if ($status == 'approved') {
                    $status = '<span class="badge bg-success">' . $status . '</span>';
                } else if ($status == 'pending') {
                    $status =  '<span class="badge bg-warning">' . $status . '</span>';
                } else {
                    $status =  '<span class="badge bg-danger">' . $status . '</span>';
                }

                return $status;
            })


            ->addColumn('approver_status', function ($row) {

                $approver_status = $row->approver_status;
                if ($approver_status == 1) {
                    $approver_status = '<span class="badge bg-success">approved</span>';
                } else if ($approver_status == 2) {
                    $approver_status = '<span class="badge bg-danger">declined</span>';
                } else {
                    $approver_status =  '<span class="badge bg-warning"></span>';
                }

                return $approver_status;
            })

            ->addColumn('amount', function ($row) {
                return number_format($row->amount, 2);
            })


            ->setRowClass(function ($row) {

                $is_declined = $row->transaction_status == 'declined' ? 'bg-gray' : '';

                return $is_declined;
            })

            ->rawColumns(['status', 'approver_status'])
            ->toJson();
    }

    public function revert_status(Request $request)
    {
        $transactions = Transactions::find($request->id);

        $transactions->transaction_status = 'pending';


        $transactions->update();
    }

    function upload_transaction_acc(Request $request)
    {


        if ($request->hasFile('importTransaction')) {

            $upload = $request->file('importTransaction');

            $fileExtension = $upload->getClientOriginalExtension();

            if ($fileExtension === 'xlsx') {
                $excel = ExcelFacade::toArray(new AccTransactionImport, $upload, Excel::XLSX);
            } elseif ($fileExtension === 'xls') {
                $excel = ExcelFacade::toArray(new AccTransactionImport, $upload, Excel::XLS);
            } else {
                $excel = ExcelFacade::toArray(new AccTransactionImport, $upload);
            }

            // return $excel;

            $arr = [];
            $data = [];

            foreach ($excel as $sheet) {
                $counter = 0;
                foreach ($sheet as $header) {
                    foreach ($header as $row) {
                        if ($counter == 0) {
                            $arr[] = $row;
                        } else {
                            $data[$counter][] = $row;
                        }
                    }
                    $counter++;
                }
            }



            $transformedData = [];
            foreach ($excel[0] as $i => $col) {
                if ($i > 0) {
                    $transformedData[] = [
                        "mobile_number" => $col[0],
                        "client_name" => $col[1],
                        "amount" => $col[2],
                        "remarks" => $col[3],
                    ];
                }
            }

            $iterate = AccTransactionListUploads::where('status', 1)->where('acc_transaction_number', $request->transactionNumber)->orderBy('upload_iteration', 'desc')->first();
            $iteration = 1;

            if ($iterate) {
                $iteration = $iterate->upload_iteration + 1;
            }

            foreach ($transformedData as $transaction) {
                AccTransactionListUploads::create([
                    'acc_transaction_number' => $request->transactionNumber,
                    'mobile_number' => $transaction['mobile_number'],
                    'client_name' => $transaction['client_name'],
                    'amount' => $transaction['amount'],
                    'remarks' => $transaction['remarks'],
                    'date_uploaded' => Carbon::now(),
                    'uploaded_by' => Auth::user()->id,
                    'upload_iteration' =>  $iteration,

                ]);
            }

            if ($request->status) {
                $transactions = Transactions::leftjoin('transaction_infos', 'transaction_infos.id', 'transactions.transaction_id')
                    ->leftjoin('acc_transaction_list_uploads', 'acc_transaction_list_uploads.acc_transaction_number', 'transaction_infos.transaction_number')
                    ->select('transactions.id', 'transactions.transaction_id', 'transactions.transaction_number', 'transactions.mobile_number', 'transactions.client_name', 'transactions.amount', 'transactions.transaction_status', 'transactions.remarks', 'transactions.user_id', 'transactions.status', 'acc_transaction_list_uploads.acc_transaction_number', 'acc_transaction_list_uploads.mobile_number', 'acc_transaction_list_uploads.client_name', 'acc_transaction_list_uploads.amount')
                    ->where('transaction_infos.transaction_number', $request->transactionNumber)
                    ->where('transactions.transaction_status', $request->status)
                    ->where('transaction_infos.status', 1)
                    ->where('transactions.status', 1)
                    ->get();
            } else {
                $transactions_col = Transactions::leftjoin('transaction_infos', 'transaction_infos.id', 'transactions.transaction_id')
                    ->select('transactions.id', 'transactions.transaction_id', 'transactions.transaction_number', 'transactions.mobile_number', 'transactions.client_name', 'transactions.amount', 'transactions.transaction_status', 'transactions.user_id', 'transactions.status')
                    ->where('transaction_infos.transaction_number', $request->transactionNumber)
                    ->where('transaction_infos.status', 1)
                    ->where('transactions.status', 1)
                    ->get();


                $transactions_acc = AccTransactionListUploads::leftjoin('transaction_infos', 'transaction_infos.transaction_number', 'acc_transaction_list_uploads.acc_transaction_number')
                    ->select('acc_transaction_list_uploads.id', 'acc_transaction_list_uploads.acc_transaction_id', 'acc_transaction_list_uploads.uploaded_by', 'acc_transaction_list_uploads.status', 'acc_transaction_list_uploads.acc_transaction_number', 'acc_transaction_list_uploads.mobile_number', 'acc_transaction_list_uploads.client_name', 'acc_transaction_list_uploads.amount', 'acc_transaction_list_uploads.transaction_status')
                    ->where('transaction_infos.transaction_number', $request->transactionNumber)
                    ->where('transaction_infos.status', 1)
                    ->where('upload_iteration', $iteration)
                    ->where('acc_transaction_list_uploads.status', 1)
                    ->get();
            }

            $col_mn = $transactions_col->pluck('mobile_number');
            $acc_mn = $transactions_acc->pluck('mobile_number');

            $all_data = $transactions_col->merge($transactions_acc);

            $grouped = $all_data->groupBy('mobile_number');

            //* mga unique na mobile number
            $uniqueMobileNumbers = $grouped->filter(function ($group) {
                return $group->count() === 1;
            });
            $uniqueMn = $uniqueMobileNumbers->keys();

            //* merong duplicate na mobile number
            $duplicates = $grouped->filter(function ($group) {
                return $group->count() > 1;
            });
            $duplicateMobileNumbers = $duplicates->keys();

            //* merge ng dalawa (wala nang duplicates)
            $uniqueTransactions = $grouped->map(function ($group) {
                return $group->first();
            });
            $data = $uniqueTransactions->flatten();
 
            //* merge ng dalawa (number only)
            // $mobile_number = $duplicateMobileNumbers->merge($uniqueMn);
            // return $mobile_number->contains("09368983300");


            return DataTables::of($data)
                ->addColumn('status', function ($row) use ($uniqueMn) {

                    $status = $row->transaction_status;
                    if (Auth::user()->user_type_id == 2) {

                        if ($status == 'approved') {
                            $status = '<span class="badge bg-success">' . $status . '</span>';
                        } else if ($status == 'pending') {
                            $status =  '<span class="badge bg-warning">' . $status . '</span>';
                        } else if ($status == 'declined') {
                            $status =  '<span class="badge bg-danger">' . $status . '</span>';
                        } else {
                            $status = '';
                        }
                    }

                    if (Auth::user()->user_type_id == 3) {
                        if ($status == 'approved') {
                            $status = '<div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success">' . $status . '</span>
                        </div>';
                        } else if($uniqueMn->contains($row->mobile_number)){
                            $status = '<span class="badge bg-pulse">no match</span>';
                        } else if ($status == 'pending') {
                            $status = '<div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning">' . $status . '</span>
                        </div>';
                        } else if ($status == 'declined') {
                            $status = '<div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger">' . $status . '</span>
                        </div>';
                        }else {
                            $status = '<span class="badge bg-pulse">no match</span>';
                        }
                    }


                    if (Auth::user()->user_type_id == 5) {
                        $status = $row->approver_status;
                        if ($status == 1) {
                            $status = '<span class="badge bg-success">approved</span>';
                        } else if ($status == 2) {
                            $status =  '<span class="badge bg-danger">declined</span>';
                        } else {
                            $status =  '<span class="badge bg-warning">declined by accounting</span>';
                        }
                    }

                    return $status;
                })

                ->addColumn('mobile_number', function ($row) use ($col_mn) {


                    $transactions = Transactions::select('mobile_number', 'transaction_number')
                        ->groupBy(['mobile_number', 'transaction_number'])
                        ->havingRaw('COUNT(*) > 1')
                        ->get();

                    $duplicate_mn = $transactions->pluck('mobile_number')->toArray();

                    $is_duplicate = in_array($row->mobile_number, $duplicate_mn);

                    if ($is_duplicate) {
                        return '<div class="ribbon ribbon-left ribbon-danger" style="min-height: unset;">
                                <div class="ribbon-box" style="margin-top: -30px; margin-bottom: 5px; padding: .5px; height: 17px;"><p style="font-size: 10px;">Duplicate</p></div>
                                    <p class="mb-0 mt-2">' . $row->mobile_number . '</p>
                            </div>';
                    } else {
                        if($col_mn->contains($row->mobile_number)){
                            return $row->mobile_number;
                        }else{
                            return '';
                        }
                    }
                })

                ->addColumn('client_name', function ($row) use ($col_mn) {

                    if($col_mn->contains($row->mobile_number)){
                        return $row->client_name;
                    }else{
                        return '';
                    }
                })

                ->addColumn('amount', function ($row) use ($col_mn) {

                    if($col_mn->contains($row->mobile_number)){
                        return number_format($row->amount, 2);
                    }else{
                        return '';
                    }
                })

                ->addColumn('acc_mn', function ($row) use ($acc_mn) {

                    if($acc_mn->contains($row->mobile_number)){
                        return $row->mobile_number;
                    }else{
                        return '';
                    }
                })

                ->addColumn('acc_cn', function ($row) use ($acc_mn) {

                    if($acc_mn->contains($row->mobile_number)){
                        return $row->client_name;
                    }else{
                        return '';
                    }
                })


                ->addColumn('acc_amount', function ($row) use ($acc_mn) {
                    if($acc_mn->contains($row->mobile_number)){
                        return number_format($row->amount, 2);
                    }else{
                        return '';
                    }
                })

                ->addColumn('upload_status', function ($row) use ($duplicateMobileNumbers) {

                    if($duplicateMobileNumbers->contains($row->mobile_number)){
                        return '<span class="text-success">MATCH</span>';
                    }else{
                        return '<span class="text-danger">NO MATCH</span>';
                    }
                })


                ->rawColumns(['status', 'acc_mn', 'acc_cn', 'acc_amount', 'mobile_number', 'upload_status'])
                ->toJson();
        } else {
            if ($request->status) {
                $transactions = Transactions::leftjoin('transaction_infos', 'transaction_infos.id', 'transactions.transaction_id')
                    ->select('transactions.*')
                    ->where('transaction_infos.transaction_number', $request->transacNum)
                    ->where('transactions.transaction_status', $request->status)
                    ->where('transaction_infos.status', 1)
                    ->where('transactions.status', 1)
                    ->get();
            } else {
                $transactions = Transactions::leftjoin('transaction_infos', 'transaction_infos.id', 'transactions.transaction_id')
                    ->select('transactions.*')
                    ->where('transaction_infos.transaction_number', $request->transacNum)
                    ->where('transaction_infos.status', 1)
                    ->where('transactions.status', 1)
                    ->get();
            }

            return DataTables::of($transactions)
                ->addColumn('status', function ($row) {

                    $status = $row->transaction_status;
                    if (Auth::user()->user_type_id == 2) {

                        if ($status == 'approved') {
                            $status = '<span class="badge bg-success">' . $status . '</span>';
                        } else if ($status == 'pending') {
                            $status =  '<span class="badge bg-warning">' . $status . '</span>';
                        } else {
                            $status =  '<span class="badge bg-danger">' . $status . '</span>';
                        }
                    }

                    if (Auth::user()->user_type_id == 3) {
                        if ($status == 'approved') {
                            $status = '<div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success">' . $status . '</span>
                    <button data-id="' . $row->id . '" class="undoStatus btn text-elegance fs-6 d-block"><i class="fa fa-arrow-rotate-left"></i></button>
                    </div>';
                        } else if ($status == 'pending') {
                            $status = '<div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning">' . $status . '</span>
                        <button data-id="' . $row->id . '" class="undoStatus btn text-elegance fs-6 d-block"><i class="fa fa-arrow-rotate-left"></i></button>
                        </div>';
                        } else {
                            $status = '<div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger">' . $status . '</span>
                        <button data-id="' . $row->id . '" class="undoStatus btn text-elegance fs-6 d-block"><i class="fa fa-arrow-rotate-left"></i></button>
                        </div>';
                        }
                    }


                    if (Auth::user()->user_type_id == 5) {
                        $status = $row->approver_status;
                        if ($status == 1) {
                            $status = '<span class="badge bg-success">approved</span>';
                        } else if ($status == 2) {
                            $status =  '<span class="badge bg-danger">declined</span>';
                        } else {
                            $status =  '<span class="badge bg-warning">declined by accounting</span>';
                        }
                    }

                    return $status;
                })

                ->addColumn('mobile_number', function ($row) {

                    $transactions = Transactions::select('mobile_number', 'transaction_number')
                        ->groupBy(['mobile_number', 'transaction_number'])
                        ->havingRaw('COUNT(*) > 1')
                        ->get();

                    $duplicate_mn = $transactions->pluck('mobile_number')->toArray();

                    $is_duplicate = in_array($row->mobile_number, $duplicate_mn);

                    if ($is_duplicate) {
                        return '<div class="ribbon ribbon-left ribbon-danger" style="min-height: unset;">
                                <div class="ribbon-box" style="margin-top: -30px; margin-bottom: 5px; padding: .5px; height: 17px;"><p style="font-size: 10px;">Duplicate</p></div>
                                    <p class="mb-0 mt-2">' . $row->mobile_number . '</p>
                            </div>';
                    } else {
                        return $row->mobile_number;
                    }
                })

                ->addColumn('amount', function ($row) {
                    return number_format($row->amount, 2);
                })

                ->addColumn('acc_mn', function ($row) {
                    return "";
                })

                ->addColumn('acc_cn', function () {
                    return "";
                })

                ->addColumn('acc_amount', function () {
                    return "";
                })

                ->addColumn('upload_status', function () {
                    return "";
                })

                ->rawColumns(['status', 'acc_mn', 'acc_cn', 'acc_amount', 'mobile_number', 'upload_status'])
                ->toJson();
        }
    }

    function upload_transaction_approver(Request $request)
    {


        if ($request->hasFile('importTransaction')) {

            $upload = $request->file('importTransaction');

            $fileExtension = $upload->getClientOriginalExtension();

            if ($fileExtension === 'xlsx') {
                $excel = ExcelFacade::toArray(new ApproverTransactionImport, $upload, Excel::XLSX);
            } elseif ($fileExtension === 'xls') {
                $excel = ExcelFacade::toArray(new ApproverTransactionImport, $upload, Excel::XLS);
            } else {
                $excel = ExcelFacade::toArray(new ApproverTransactionImport, $upload);
            }

            // return $excel;

            $arr = [];
            $data = [];

            foreach ($excel as $sheet) {
                $counter = 0;
                foreach ($sheet as $header) {
                    foreach ($header as $row) {
                        if ($counter == 0) {
                            $arr[] = $row;
                        } else {
                            $data[$counter][] = $row;
                        }
                    }
                    $counter++;
                }
            }



            $transformedData = [];
            foreach ($excel[0] as $i => $col) {
                if ($i > 0) {
                    $transformedData[] = [
                        "mobile_number" => $col[0],
                        "client_name" => $col[1],
                        "amount" => $col[2],
                        "remarks" => $col[3],
                    ];
                }
            }

            $iterate = ApproverTransactionListUploads::where('status', 1)->where('approver_transaction_number', $request->transactionNumber)->orderBy('upload_iteration', 'desc')->first();
            $iteration = 1;

            if ($iterate) {
                $iteration = $iterate->upload_iteration + 1;
            }


            foreach ($transformedData as $transaction) {
                ApproverTransactionListUploads::create([
                    'approver_transaction_number' => $request->transactionNumber,
                    'mobile_number' => $transaction['mobile_number'],
                    'client_name' => $transaction['client_name'],
                    'amount' => $transaction['amount'],
                    'remarks' => $transaction['remarks'],
                    'date_uploaded' => Carbon::now(),
                    'uploaded_by' => Auth::user()->id,
                    'upload_iteration' =>  $iteration,

                ]);
            }

            if ($request->status) {
                $transactions = Transactions::leftjoin('transaction_infos', 'transaction_infos.id', 'transactions.transaction_id')
                    ->leftjoin('acc_transaction_list_uploads', 'acc_transaction_list_uploads.acc_transaction_number', 'transaction_infos.transaction_number')
                    ->select('transactions.id', 'transactions.transaction_id', 'transactions.transaction_number', 'transactions.mobile_number', 'transactions.client_name', 'transactions.amount', 'transactions.transaction_status', 'transactions.remarks', 'transactions.user_id', 'transactions.status', 'acc_transaction_list_uploads.acc_transaction_number', 'acc_transaction_list_uploads.mobile_number', 'acc_transaction_list_uploads.client_name', 'acc_transaction_list_uploads.amount')
                    ->where('transaction_infos.transaction_number', $request->transactionNumber)
                    ->where('transactions.transaction_status', $request->status)
                    ->where('transaction_infos.status', 1)
                    ->where('transactions.status', 1)
                    ->get();
            } else {
                $transactions_acc = Transactions::leftjoin('transaction_infos', 'transaction_infos.id', 'transactions.transaction_id')
                    ->select('transactions.id', 'transactions.transaction_id', 'transactions.transaction_number', 'transactions.mobile_number', 'transactions.client_name', 'transactions.amount', 'transactions.transaction_status', 'transactions.user_id', 'transactions.status')
                    ->where('transaction_infos.transaction_number', $request->transactionNumber)
                    ->where('transaction_infos.status', 1)
                    ->where('transactions.status', 1)
                    ->orderBy('transactions.approver_status', 'asc')
                    ->get();


                $transactions_approver = ApproverTransactionListUploads::leftjoin('transaction_infos', 'transaction_infos.transaction_number', 'approver_transaction_list_uploads.approver_transaction_number')
                    ->select('approver_transaction_list_uploads.id', 'approver_transaction_list_uploads.uploaded_by', 'approver_transaction_list_uploads.status', 'approver_transaction_list_uploads.approver_transaction_number', 'approver_transaction_list_uploads.mobile_number', 'approver_transaction_list_uploads.client_name', 'approver_transaction_list_uploads.amount', 'approver_transaction_list_uploads.transaction_status')
                    ->where('transaction_infos.transaction_number', $request->transactionNumber)
                    ->where('transaction_infos.status', 1)
                    ->where('upload_iteration', $iteration)
                    ->where('approver_transaction_list_uploads.status', 1)
                    ->get();
            }

            $col_mn = $transactions_acc->pluck('mobile_number');
            $approver_mn = $transactions_approver->pluck('mobile_number');

            $all_data = $transactions_acc->merge($transactions_approver);

            // return $all_data;

            $grouped = $all_data->groupBy('mobile_number');

            //* mga unique na mobile number
            $uniqueMobileNumbers = $grouped->filter(function ($group) {
                return $group->count() === 1;
            });
            $uniqueMn = $uniqueMobileNumbers->keys();

            //* merong duplicate na mobile number
            $duplicates = $grouped->filter(function ($group) {
                return $group->count() > 1;
            });
            $duplicateMobileNumbers = $duplicates->keys();

            // return $duplicateMobileNumbers;

            //* merge ng dalawa (wala nang duplicates)
            $uniqueTransactions = $grouped->map(function ($group) {
                return $group->first();
            });
            $data = $uniqueTransactions->flatten();
 
            //* merge ng dalawa (number only)
            // $mobile_number = $duplicateMobileNumbers->merge($uniqueMn);
            // return $mobile_number->contains("09368983300");


            return DataTables::of($data)
                ->addColumn('status', function ($row) use ($uniqueMn) {

                    $status = $row->transaction_status;

                    if (Auth::user()->user_type_id == 5) {
                        $status = $row->approver_status;
                        if ($status == 1) {
                            $status = '<span class="badge bg-success">approved</span>';
                        } else if($uniqueMn->contains($row->mobile_number)){
                            $status = '<span class="badge bg-pulse">no match</span>';
                        } else if ($status == 2) {
                            $status =  '<span class="badge bg-danger">declined</span>';
                        }else if ($row->approver_status == 'declined') {
                            $status =  '<span class="badge bg-warning">declined by accounting</span>';
                        } else {
                            $status =  '<span class="badge bg-pulse">no match</span>';
                        }
                    }

                    return $status;
                })

                ->addColumn('mobile_number', function ($row) use ($col_mn) {


                    $transactions = Transactions::select('mobile_number', 'transaction_number')
                        ->groupBy(['mobile_number', 'transaction_number'])
                        ->havingRaw('COUNT(*) > 1')
                        ->get();

                    $duplicate_mn = $transactions->pluck('mobile_number')->toArray();

                    $is_duplicate = in_array($row->mobile_number, $duplicate_mn);

                    if ($is_duplicate) {
                        return '<div class="ribbon ribbon-left ribbon-danger" style="min-height: unset;">
                                <div class="ribbon-box" style="margin-top: -30px; margin-bottom: 5px; padding: .5px; height: 17px;"><p style="font-size: 10px;">Duplicate</p></div>
                                    <p class="mb-0 mt-2">' . $row->mobile_number . '</p>
                            </div>';
                    } else {
                        if($col_mn->contains($row->mobile_number)){
                            return $row->mobile_number;
                        }else{
                            return '';
                        }
                    }
                })

                ->addColumn('client_name', function ($row) use ($col_mn) {

                    if($col_mn->contains($row->mobile_number)){
                        return $row->client_name;
                    }else{
                        return '';
                    }
                })

                // ->addColumn('amount', function ($row) use ($col_mn) {

                //     if($col_mn->contains($row->mobile_number)){
                //         return number_format($row->amount, 2);
                //     }else{
                //         return '';
                //     }
                // })

                ->addColumn('acc_mn', function ($row) use ($col_mn) {

                    if($col_mn->contains($row->mobile_number) && $row->transaction_status == "approved"){
                        return $row->mobile_number;
                    }else{
                        return '';
                    }
                })

                ->addColumn('acc_cn', function ($row) use ($col_mn) {

                    if($col_mn->contains($row->mobile_number) && $row->transaction_status == "approved"){
                        return $row->client_name;
                    }else{
                        return '';
                    }
                })

                ->addColumn('approver_mn', function ($row) use ($approver_mn) {

                    if($approver_mn->contains($row->mobile_number)){
                        return $row->mobile_number;
                    }else{
                        return '';
                    }
                })

                ->addColumn('approver_cn', function ($row) use ($approver_mn) {

                    if($approver_mn->contains($row->mobile_number)){
                        return $row->client_name;
                    }else{
                        return '';
                    }
                })


                // ->addColumn('acc_amount', function ($row) use ($approver_mn) {
                //     if($approver_mn->contains($row->mobile_number)){
                //         return number_format($row->amount, 2);
                //     }else{
                //         return '';
                //     }
                // })

                ->addColumn('approver_status', function ($row) use($uniqueMn) {

                    $status = $row->approver_status;
                    if ($status == 1) {
                        $status = '<span class="uploadStatus badge bg-success">approved</span>';
                    } else if ($status == 2) {
                        $status =  '<span class="uploadStatus badge bg-danger">declined</span>';
                    } else if($row->transaction_status == "declined" || $uniqueMn->contains($row->mobile_number)) {
                        $status = '';
                    }else{
                        $status =  '<span class="uploadStatus badge bg-warning">pending</span>';
                    }
    
                    return $status;
                })

                ->addColumn('upload_status', function ($row) use ($duplicateMobileNumbers) {

                    if($duplicateMobileNumbers->contains($row->mobile_number) && $row->transaction_status == "approved"){
                        return '<span class="text-success">MATCH</span>';
                    }else if($row->transaction_status == "declined"){
                        return '<span class="text-danger">DECLINED (ACCT)</span>';
                    }else{
                        return '<span class="text-danger">NO MATCH</span>';
                    }
                })

                ->addColumn('acc_status', function ($row) {

                    $status = $row->transaction_status;
                    if ($status == 'approved') {
                        $status = '<span class="badge bg-success">' . $status . '</span>';
                    } else if ($status == 'pending') {
                        $status =  '<span class="badge bg-warning">' . $status . '</span>';
                    } else {
                        $status =  '<span class="badge bg-danger">' . $status . '</span>';
                    }
    
                    return $status;
                })


                ->setRowClass(function ($row) {

                    $is_declined = $row->transaction_status == 'declined' ? 'bg-gray' : '';
    
                    return $is_declined;
                })

                ->rawColumns(['status', 'acc_mn', 'acc_cn', 'acc_status', 'mobile_number', 'approver_status', 'upload_status'])
                ->toJson();
        } else {
            if ($request->status) {
                $transactions = Transactions::leftjoin('transaction_infos', 'transaction_infos.id', 'transactions.transaction_id')
                    ->select('transactions.*')
                    ->where('transaction_infos.transaction_number', $request->transacNum)
                    ->where('transactions.transaction_status', $request->status)
                    ->where('transaction_infos.status', 1)
                    ->where('transactions.status', 1)
                    ->get();
            } else {
                $transactions = Transactions::leftjoin('transaction_infos', 'transaction_infos.id', 'transactions.transaction_id')
                    ->select('transactions.*')
                    ->where('transaction_infos.transaction_number', $request->transacNum)
                    ->where('transaction_infos.status', 1)
                    ->where('transactions.status', 1)
                    ->orderBy('transactions.approver_status', 'asc')
                    ->get();
            }

            return DataTables::of($transactions)
                ->addColumn('status', function ($row) {

                    if (Auth::user()->user_type_id == 5) {
                        $status = $row->approver_status;
                        if ($status == 1) {
                            $status = '<span class="badge bg-success">approved</span>';
                        } else if ($status == 2) {
                            $status =  '<span class="badge bg-danger">declined</span>';
                        } else {
                            $status =  '<span class="badge bg-warning">declined by accounting</span>';
                        }
                    }

                    return $status;
                })

                ->addColumn('mobile_number', function ($row) {

                    $transactions = Transactions::select('mobile_number', 'transaction_number')
                        ->groupBy(['mobile_number', 'transaction_number'])
                        ->havingRaw('COUNT(*) > 1')
                        ->get();

                    $duplicate_mn = $transactions->pluck('mobile_number')->toArray();

                    $is_duplicate = in_array($row->mobile_number, $duplicate_mn);

                    if ($is_duplicate) {
                        return '<div class="ribbon ribbon-left ribbon-danger" style="min-height: unset;">
                                <div class="ribbon-box" style="margin-top: -30px; margin-bottom: 5px; padding: .5px; height: 17px;"><p style="font-size: 10px;">Duplicate</p></div>
                                    <p class="mb-0 mt-2">' . $row->mobile_number . '</p>
                            </div>';
                    } else {
                        return $row->mobile_number;
                    }
                })

                // ->addColumn('amount', function ($row) {
                //     return number_format($row->amount, 2);
                // })

                ->addColumn('acc_mn', function ($row) {


                    if ($row->transaction_status == 'approved') {
                        return $row->mobile_number;
                    } else {
                        return '';
                    }
                })
    
                ->addColumn('acc_cn', function ($row) {
    
    
                    if ($row->transaction_status == 'approved') {
                        return $row->client_name;
                    } else {
                        return '';
                    }
                })

                ->addColumn('approver_mn', function ($row) {
                        return '';
                })

                ->addColumn('approver_cn', function ($row) {
                        return '';
                })

                // ->addColumn('acc_amount', function () {
                //     return "";
                // })

                ->addColumn('acc_status', function ($row) {

                    $status = $row->transaction_status;
                    if ($status == 'approved') {
                        $status = '<span class="badge bg-success">' . $status . '</span>';
                    } else if ($status == 'pending') {
                        $status =  '<span class="badge bg-warning">' . $status . '</span>';
                    } else {
                        $status =  '<span class="badge bg-danger">' . $status . '</span>';
                    }
    
                    return $status;
                })

                ->addColumn('approver_status', function ($row) {

                    $status = $row->approver_status;
                    if ($status == 1) {
                        $status = '<span class="badge bg-success">approved</span>';
                    } else if ($status == 2) {
                        $status =  '<span class="badge bg-danger">declined</span>';
                    } else if($row->transaction_status == "declined") {
                        $status = '';
                    }else{
                        $status =  '<span class="badge bg-warning">pending</span>';
                    }
    
                    return $status;
                })

                ->addColumn('upload_status', function ($row) {

                    return "";
                })

                ->setRowClass(function ($row) {

                    $is_declined = $row->transaction_status == 'declined' ? 'bg-gray' : '';
    
                    return $is_declined;
                })

                

                ->rawColumns(['status', 'acc_mn', 'acc_cn', 'acc_amount','acc_status', 'mobile_number', 'approver_status', 'upload_status'])
                ->toJson();
        }
    }
};
