<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


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

    public function followers($id){
            $checkUser = User::find($id);

            if(isset($checkUser)){

                $user = User::select('users.id', 'users.name', 'users.image as profilePicture',
            DB::raw("(SELECT COUNT(*) FROM follows WHERE follows.follower = $id) as follower_count"),
            DB::raw("(SELECT COUNT(*) FROM follows WHERE follows.followed = $id) as followed_count"))
            ->join('follows', 'users.id', '=', 'follows.follower')
            ->where('follows.followed', '=', $id)
            ->get();
                if($user->isNotEmpty()){
                    $user = getImages($user);
                } else {
                    return response()->json([
                        'message' => 'No tienes seguidores'
                    ], 200);
                } 
            } else{
                return response()->json([
                    'message' => 'ID no encontrada'
                ], 404);
            }
            return response()->json([
                'message' => 'Seguidores obtenidos con éxito',
                'followers' => $user
            ], 200);
    }

    public function follows($id){
        $checkUser = User::find($id);

        if(isset($checkUser)){

            $user = User::select('users.id', 'users.name', 'users.image as profilePicture',
            DB::raw("(SELECT COUNT(*) FROM follows WHERE follows.follower = $id) as follower_count"),
            DB::raw("(SELECT COUNT(*) FROM follows WHERE follows.followed = $id) as followed_count"))
            ->join('follows', 'users.id', '=', 'follows.followed')
            ->where('follows.follower', '=', $id)
            ->get();
            if($user->isNotEmpty()){
                $user = getImages($user);
            } else {
                return response()->json([
                    'message' => 'No sigues a nadie'
                ], 200);
            } 
        } else{
            return response()->json([
                'message' => 'ID no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'Gente que sigues obtenida con éxito',
            'followers' => $user
        ], 200);
    }
}