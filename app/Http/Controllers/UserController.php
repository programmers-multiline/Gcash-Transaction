<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    public function auth_login(Request $request){
        
        // check if user is exists
        $user = User::where('username', $request->login_username)->where('password', $request->login_password)->first();

        if(!$user) {
            return redirect()->back()->withInput($request->input())->withError('invalid Credential');
        }

        // Logged
        Auth::login($user);

        return redirect()->intended('dashboard')->withSuccess('Login Successfully');

    }


    public function auth_logout()
    {
        Auth::logout();
    
        return redirect('/');
    }
    
}
