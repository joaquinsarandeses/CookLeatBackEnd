<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Favorite;
use App\Models\Recipe;


class FavoritesController extends Controller
{

    //DELETE /favorite/delete
    public function delete(Request $request){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $json = $request->getContent();

        $datos = json_decode($json);

        if($datos){
            if(isset($datos->user, $datos->recipe)){

        $favorite = Favorite::where('user_id', '=', $datos->user)
        ->where('recipe_id', '=', $datos->recipe);

        try{
        $favorite->delete();
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
    
    //PUT /favorite/create
    public function create(Request $request){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $json = $request->getContent();

        $datos = json_decode($json);

        if($datos){
            if(isset($datos->user, $datos->recipe)){
            $favorite = new Favorite();
            $favorite->user_id = $datos->user;
            $favorite->recipe_id = $datos->recipe;
            try{
                $favorite->save();
                $response["data"] = "ID: $favorite->id";
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
            //return response()->json($response);
            return $favorite;
    }
}