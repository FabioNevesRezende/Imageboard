<?php

namespace Ibbr\Http\Controllers;

use Illuminate\Http\Request;
use Ibbr\User;
use Auth;
use Redirect;
use Purifier;
use Hash;

class UserController extends Controller
{
    public function updatePassword(Request $request){
        if(Auth::check()){
            $user = Auth::user();
            if($user && $request->password === $request->confirm_password){
                $user->password = Hash::make($request->password);
                
                $user->save();
                return Redirect('/admin');
            }
        }
        return Redirect('/');
    }
}
