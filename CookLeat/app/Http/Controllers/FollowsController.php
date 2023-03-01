<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;


class FollowsController extends Controller
{

    //DELETE /follow/delete
    public function delete(Request $request){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $json = $request->getContent();

        $datos = json_decode($json);

        if($datos){
            if(isset($datos->follower, $datos->followed)){

        $follow = Follow::where('follower', '=', $datos->follower)
        ->where('followed', '=', $datos->followed);

        try{
        $follow->delete();
         }catch(\Exception $e){
            $response["status"] = "error inesperado al eliminar";
        $response["code"] = 413;
        }

    } else{
        $response["status"] = "Faltan parametros";
        $response["code"] = 415;
    }

} else {
    $response["status"] = "JSON incorrecto";
    $response["code"] = 412;
}     
return response()->json($response);
}

    //PUT /follow/create
    public function create(Request $request){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $json = $request->getContent();

        $datos = json_decode($json);

        if($datos){
            if(isset($datos->follower, $datos->followed)){
                $follow = new Follow();
            $follow->follower = $datos->follower;
            $follow->followed = $datos->followed;
            try{
                $follow->save();
                $response["data"] = "ID: $follow->id";
                }catch(\Exception $e){
                    $response["status"] = "error al guardar";
                $response["code"] = 413;
                }
            } else{
                    $response["status"] = "Faltan parametros";
                    $response["code"] = 415;
                }
    
            } else {
                $response["status"] = "JSON incorrecto";
                $response["code"] = 412;
            }     
            return response()->json($response);
    }
}