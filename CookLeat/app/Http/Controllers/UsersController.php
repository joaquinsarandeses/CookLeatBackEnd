<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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

//PUT /users/registro
function registro(Request $request){
    $json = $request->getContent();

    $validator =  Validator::make(json_decode($json, true),[
        'name'=>'required|min:4|max:13|unique:users,name',
        'email'=>'required|email|max:40|unique:users,email',
        'password'=>['required', Password::min(8)->mixedCase(),'regex:/[0-9]/'], 'max:20',
    ]);
    if ($validator->fails()) {
        Log::warning('Registration validation failed.', [
            'input' => $request->all(),
            'errors' => $validator->errors(),
        ]);
        return response()->json([
            'message' => ['Ha habido un error validando la información introducida.'],
            'errors' => $validator->errors()
        ], 422);
    }else{
    $users = new User();
    $users->name = $request->name;
    $users->email = $request->email;
    $users->password = Hash::make($request->password);
    try {
        $users->save();
    } catch (Exception $e){
        Log::error('User registration failed.', [
            'input' => $request->all(),
            'error_message' => $e->getMessage(),
        ]);

        return response()->json([
            'message' => ['Ha habido un error con el servidor.']
        ], 500);
    }
    Log::info('User registered successfully.', [
        'input' => $request->all(),
        'user_id' => $users->id,
    ]);
    }
    return response()->json([
        'message' => ['Te has registrado :D.']
    ], 201);
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
