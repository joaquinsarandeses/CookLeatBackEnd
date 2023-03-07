<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecoverPasswordMailable;



class PasswordsController extends Controller
{

    //GET /password/recover
    public function recover(Request $request){
            $json = $request->getContent();

            $datos = json_decode($json);

            if($datos){
                if(isset($datos->email)){

            $user = User::where('email', '=', $datos->email)
            ->get();

            if($user->isNotEmpty()){
                $password = generateRandomString();
                try{
                    $mail = Mail::to($datos->email)->send(new RecoverPasswordMailable($user[0]->name, $password));
                    }catch(\Exception $e){
                       return response()->json([
                            'message' => "Error al mandar correo de recuperacion"
                        ], 404);
                    }
            } else {
                return response()->json([
                    'message' => 'Correo no asociado a ninguna cuenta'
                ], 404);
            }
            

        } else{
            return response()->json([
                'message' => 'Parametros incorrectos o insuficientes'
            ], 404);
        }

        } else {
            return response()->json([
                'message' => 'Formato de peticion incorrecto'
            ], 404);
        }     
        return response()->json([
           'mail' => $mail
        ], 200);
    }
}