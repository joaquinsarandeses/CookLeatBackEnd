<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;


class FollowsController extends Controller
{

    //DELETE /follow/delete
    public function delete(Request $request){
            $json = $request->getContent();

            $datos = json_decode($json);

            if($datos){
                if(isset($datos->id, $datos->follow)){

            $follow = Follow::where('follower', '=', $datos->id)
            ->where('followed', '=', $datos->follow);

            try{
            $follow->delete();
            }catch(\Exception $e){
                return response()->json([
                    'message' => 'Fallo al dejar de seguir'
                ], 400);
            }

        } else{
            return response()->json([
                'message' => 'Parametros incorrectos o insuficientes'
            ], 403);
        }

        } else {
            return response()->json([
                'message' => 'Formato de peticion incorrecto'
            ], 404);
        }     
        return response()->json([
            'message' => 'Ya no sigues a esta persona'
        ], 200);
    }

    //PUT /follow/create
    public function create(Request $request){

        $json = $request->getContent();

        $datos = json_decode($json);

        if($datos){
            if(isset($datos->id, $datos->follow)){
                $follow = new Follow();
            $follow->follower = $datos->id;
            $follow->followed = $datos->follow;
            try{
                $follow->save();
                }catch(\Exception $e){
                    return response()->json([
                        'message' => 'Fallo al seguir'
                    ], 404);
                }
            } else{
                return response()->json([
                    'message' => 'Parametros incorrectos o insuficientes'
                ], 403);
                }
    
            } else {
                return response()->json([
                    'message' => 'Formato de peticion incorrecto'
                ], 404);
            }     
            return response()->json([
                'message' => 'Ahora sigues a este usuario'
            ], 200);
    }
}