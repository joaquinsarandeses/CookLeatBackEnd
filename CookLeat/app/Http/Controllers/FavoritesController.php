<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Favorite;
use App\Models\Recipe;


class favoritesController extends Controller
{

    //DELETE /favorite/delete
    public function delete($id){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $favorite = Favorite::find($id);

        
        $favorite->delete();
    }//PUT /favorite/create
    public function create(Request $request){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $json = $request->getContent();

        $datos = json_decode($json);

        if($datos){
            if(isset($datos->user_id, $datos->recipe_id)){
                $favorite = new Favorite();
            $favorite->user_id = $datos->user_id;
            $favorite->recipe_id = $datos->recipe_id;
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
            return response()->json($response);
    }
}