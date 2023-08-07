<?php

namespace Ibbr\Http\Controllers;

use Illuminate\Http\Request;
use Ibbr\User;
use Auth;
use Redirect;
use Purifier;
use Hash;
use Ibbr\Helpers\Funcoes;

class UserController extends Controller
{
    public function defineArrayValidacao(){
        $regras = array();
        $regras['password'] = 'required|max:25';
        $regras['confirm_password'] = 'required|max:25';
        $regras['old_password'] = 'required|max:25';
        
        return $regras;
    }
    
    public function updatePassword(Request $request){
        if(Auth::check()){
            Funcoes::consolelog('UserController::updatePassword');
            $user = Auth::user();
            $regras = $this->defineArrayValidacao();
            $this->validate($request, $regras);
            if(!$user)
            {
                return $this->redirecionaComMsg('erro_admin', 'Erro inesperado', '/admin');
            }
            if($request->password !== $request->confirm_password)
            {
                return $this->redirecionaComMsg('erro_admin', 'A nova senha e sua confirmação não batem', '/admin');
            }
            if(!Hash::check($request->old_password, $user->password))
            {
                return $this->redirecionaComMsg('erro_admin', 'Erro ao validar senha antiga', '/admin');
            }
            $user->password = Hash::make($request->password);
                
            $user->save();
            return $this->redirecionaComMsg('sucesso_admin', 'Senha alterada com sucesso', '/admin');
            
        }
        return Redirect('/');
    }
}
