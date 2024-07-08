<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\TransactionUploads;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard(){

        $uploads = TransactionUploads::where('status', 1)->where('user_id', Auth::user()->id)->count();
        $pending_transactions = Transactions::where('status', 1);
        $approved_transactions = Transactions::where('status', 1);
        $declined_transactions = Transactions::where('status', 1);

        if(Auth::user()->user_type_id == 2){
            $approved_transactions = $approved_transactions->where('transaction_status', 'approved')->where('user_id', Auth::user()->id);
            $declined_transactions = $declined_transactions->where('transaction_status', 'declined')->where('user_id', Auth::user()->id);
        }else if(Auth::user()->user_type_id == 3){
            $pending_transactions = $pending_transactions->where('transaction_status', 'pending');
            $approved_transactions = $approved_transactions->where('transaction_status', 'approved')->where('acc_approver', Auth::user()->id);
            $declined_transactions = $declined_transactions->where('transaction_status', 'declined')->where('acc_approver', Auth::user()->id);
        }else if(Auth::user()->user_type_id == 5){
            $pending_transactions = $pending_transactions->whereNull('approver_status');
            $approved_transactions = $approved_transactions->where('approver_status', 1)->where('approved_by', Auth::user()->id);
            $declined_transactions = $declined_transactions->where('approver_status', 2)->where('approved_by', Auth::user()->id);
        }

        $pending_transactions = $pending_transactions->count();
        $approved_transactions = $approved_transactions->count();
        $declined_transactions = $declined_transactions->count();

        return view('/dashboard', compact('uploads', 'pending_transactions', 'approved_transactions', 'declined_transactions'));

    }
}
