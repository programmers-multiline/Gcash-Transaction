<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\TransactionInfo;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function fetch_transactions(Request $request)
    {

        $transactions = TransactionInfo::leftjoin('users', 'users.id', 'transaction_infos.created_by')
            ->select('transaction_infos.*', 'users.firstname as fn', 'users.lastname as ln')
            ->where('transaction_infos.status', 1)
            ->where('users.status', 1);

        if ($request->path == "pages/transactions_acc") {
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
                if(Auth::user()->user_type_id == 2){

                    if ($status == 'approved') {
                        $status = '<span class="badge bg-success">' . $status . '</span>';
                    } else if ($status == 'pending') {
                        $status =  '<span class="badge bg-warning">' . $status . '</span>';
                    } else {
                        $status =  '<span class="badge bg-danger">' . $status . '</span>';
                    }
                }

                if(Auth::user()->user_type_id == 3){
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

            ->addColumn('mobile_number', function ($row){

                $transactions = Transactions::select('mobile_number', 'transaction_number')
                ->groupBy(['mobile_number', 'transaction_number'])
                ->havingRaw('COUNT(*) > 1')
                ->get();

                $duplicate_mn = $transactions->pluck('mobile_number')->toArray();

                $is_duplicate = in_array($row->mobile_number, $duplicate_mn);

                if($is_duplicate){
                    return '<div class="ribbon ribbon-left ribbon-danger" style="min-height: unset;">
                                <div class="ribbon-box" style="margin-top: -30px; margin-bottom: 5px; padding: .5px; height: 17px;"><p style="font-size: 10px;">Duplicate</p></div>
                                    <p class="mb-0 mt-2">'.$row->mobile_number.'</p>
                            </div>';
                }else{
                    return $row->mobile_number;
                }
                 
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

            ->addColumn('amount', function ($row) {
                return number_format($row->amount, 2);
            })

            ->rawColumns(['status', 'total_number_approved', 'total_number_declined', 'mobile_number'])
            ->toJson();
    }




    public function fetch_transactions_approved(Request $request)
    {
        $transactions_approved = TransactionInfo::leftjoin('transactions', 'transactions.transaction_id', 'transaction_infos.id')
            ->select('transactions.*')
            ->where('transactions.status', 1)
            ->where('transaction_infos.status', 1)
            ->where('transaction_status', 'approved')
            ->where('transactions.transaction_number', $request->transacNum);

        if ($request->status) {
            $transactions_approved = TransactionInfo::leftjoin('transactions', 'transactions.transaction_id', 'transaction_infos.id')
                ->select('transactions.*')
                ->where('transactions.status', 1)
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
                return number_format($row->amount, 2);
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
        } else {
            $transac = Transactions::where('transactions.status', 1)
                ->where('transactions.transaction_id', $transaction->transaction_id)
                ->get();

            $transaction_status = collect($transac)->pluck('transaction_status')->toArray();


            $in_array = in_array('pending', $transaction_status);

            if (!$in_array) {
                $ti = TransactionInfo::find($transaction->transaction_id);
                $ti->progress = "done";
                $ti->update();
            }
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
        } else {
            $transac = Transactions::where('transactions.status', 1)
                ->where('transactions.transaction_id', $transaction->transaction_id)
                ->get();

            $transaction_status = collect($transac)->pluck('transaction_status')->toArray();


            $in_array = in_array('pending', $transaction_status);

            if (!$in_array) {
                $ti = TransactionInfo::find($transaction->transaction_id);
                $ti->progress = "done";
                $ti->update();
            }
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

    public function revert_status(Request $request){
        $transactions = Transactions::find($request->id);

        $transactions->transaction_status = 'pending';
        

        $transactions->update();
    }
};
