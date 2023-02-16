<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    ////GET /users/list?filtros
    public function list(){

        $user = Users::all();
        return $user;

    }

    public function show($id){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $user = Users::find($id);
        if ($user){
            $response["data"] = $user;
        } else{
            $response["status"] = "user no existente";
                $response["code"] = 14;

        }
        return response()->json($response);
    }

//PUT /users/create
    public function create(Request $request){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $json = $request->getContent();

        $datos = json_decode($json);

        if($datos){
            if(isset($datos->name, $datos->email, $datos->password)){

            $user = new User();
            $user->name = $datos->name;
            $user->email = $datos->email;
            $user->password = $datos->password;
            
            try{
            $user->save();
            $response["data"] = "ID: $user->id";
            }catch(\Exception $e){
                $response["status"] = "error al guardar";
            $response["code"] = 13;
            }
        } else{
                $response["status"] = "Faltan parametros";
                $response["code"] = 15;
            }

        } else {
            $response["status"] = "JSON incorrecto";
            $response["code"] = 12;
        }     
        return response()->json($response);
}           

//POST /users/update/ID
public function update(Request $request, $id){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $json = $request->getContent();

        $datos = json_decode($json);

        if($datos){
            $user = User::find($id);
            if ($user){
                if(isset($datos->image)){
                        $response["status"] = "imagen actualizada correctamente";
                        $user->image = $datos->image;               
            }
            
                try{
                $user->save();
                }catch(\Exception $e){
                    $response["status"] = "error al guardar";
                $response["code"] = 13;
                }
            }else {
                $response["status"] = "user no existente";
                $response["code"] = 14;
            }
        
        } else {
            $response["status"] = "JSON incorrecto";
            $response["code"] = 12;
        }
        return response()->json($response);
    }
}
