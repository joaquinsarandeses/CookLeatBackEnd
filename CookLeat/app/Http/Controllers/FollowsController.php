<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;


class FollowsController extends Controller
{

    //DELETE /follow/delete
    public function delete($id){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $follow = Follow::find($id);

        
        $follow->delete();
    }//PUT /follow/create
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