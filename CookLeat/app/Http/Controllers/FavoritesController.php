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

            $json = $request->getContent();

            $datos = json_decode($json);

            if($datos){
                if(isset($datos->user, $datos->recipe)){

            $favorite = Favorite::where('user_id', '=', $datos->user)
            ->where('recipe_id', '=', $datos->recipe);

            try{
            $favorite->delete();
            }catch(\Exception $e){
                return response()->json([
                    'message' => 'Error al eliminar'
                ], 415);
            }
            } else{
                return response()->json([
                    'message' => 'Faltan parametros'
                ], 415);
            }
        } else {
            return response()->json([
                'message' => 'JSON incorrecto'
            ], 415);
        }

        return response()->json([
            'message' => 'Favorito eliminado'
        ], 200);
    }
    
    //PUT /favorite/create
    public function create(Request $request){

        $json = $request->getContent();

        $datos = json_decode($json);

        if($datos){
            if(isset($datos->user, $datos->recipe)){
            $favorite = new Favorite();
            $favorite->user_id = $datos->user;
            $favorite->recipe_id = $datos->recipe;
            try{
                $favorite->save();
                }catch(\Exception $e){
                    return response()->json([
                        'message' => 'error al guardar'
                    ], 415);
                }
            } else{
                return response()->json([
                    'message' => 'Faltan parametros'
                ], 415);
                }
    
            } else {
                return response()->json([
                    'message' => 'JSON incorrecto'
                ], 415);
            }     
            return response()->json([
                'message' => 'Favorito agregado'
            ], 200);
    }

    public function check(Request $request){

        $json = $request->getContent();

        $datos = json_decode($json);

        if($datos){
            if(isset($datos->user, $datos->recipe)){

        $checkFavorite = Favorite::where('user_id', '=', $datos->user)
        ->where('recipe_id', '=', $datos->recipe)
        ->get();

        if($checkFavorite->isNotEmpty()){
            $favorite = true;
            return response()->json([
                'message' => 'Receta favorita',
                'favorite' => $favorite
            ], 200);
        } else {
            $favorite = false;
            return response()->json([
                'message' => 'Receta no favortia',
                'favorite' => $favorite
            ], 200);
        }

        
        } else{
            return response()->json([
                'message' => 'Faltan parametros'
            ], 415);
        }
        } else {
            return response()->json([
                'message' => 'Json incorrecto'
            ], 415);
        }


    }
}