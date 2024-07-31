<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\GoogleFormResponses;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class GoogleSheetController extends Controller
{

    public function getSheetData()
    {
        $url = 'https://script.google.com/macros/s/AKfycbzfLPzSKkKBcRiUSM2srlbAYBo-NPAPqsn5IUXFFcm6Zb0XjD-aSeq6qykBvGqr-AX4Jw/exec';
        $response = Http::get($url);

        if ($response->successful()) {
            $responseData = $response->json();
            foreach($responseData as $data){
                GoogleFormResponses::create([
                    'client_id' => $data['Client ID'],
                    'branch_name' => $data['Branch Name'],
                    'firstname' => $data['First Name'],
                    'lastname' => $data['Last Name'],
                    'mi' => $data['Middle Initial'],
                    'suffix' => $data['Suffix (Optional)'],
                    'pension_number' => $data['Pension Number'],
                    'pension_type' => $data['Type of Pension'],
                    'gcash_number' => $data['G-Cash Number'],
                    'gcash_name' => $data['G-Cash Name'],
                    'date_created' => Carbon::parse($data['Timestamp'])->toDateString(),
                ]);
            }

        } else {
            return response()->json(['message' => 'Failed to fetch data.'], $response->status());
        }
    }


    public function webhook(Request $request) {
        Log::info($request['Question 1']);
    }

}
