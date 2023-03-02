<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class UsersController extends Controller
{
    ////GET /users/list?filtros
    public function list(){

        $user = Users::all();
        return $user;

    }

    public function show(Request $request){

        $checkUser = User::find($request->id);

        if(isset($checkUser)){

        $user = User::select('users.id', 'users.name', 'users.image', 'followed.cnt as follows', 'follower.cnt as followers')
        ->leftJoin(DB::raw("(select follower, count(*) cnt from Follows where follower = $request->id) as followed"), 'followed.follower', '=', 'users.id')
        ->leftJoin(DB::raw("(select followed, count(*) cnt from Follows where followed = $request->id) as follower"), 'follower.followed', '=', 'users.id')
        ->where('users.id', '=', $request->id)
        ->get();
       
 
        } else{
            return response()->json([
                'message' => 'ID no encontrada'
            ], 404);

        }
        foreach ($user as $profile) { 
            $userRoute = $profile['image'];
            $userPath = storage_path('app/' . $userRoute);
            if (!file_exists($userPath)) {
                return response()->json(['message' => 'Image not found'], 404);
             // return $recipePath;
            } else{
                $file = file_get_contents($userPath);
                $encodedData = base64_encode($file);
               // $encodedData = str_replace('+', '-', $encodedData);
               // $encodedData = str_replace('/', '_', $encodedData);
               // $encodedData = rtrim($encodedData, '=');
                $profile['image'] = base64_encode($file);
               // return $favorite;
            }
            if(is_null($profile["followers"])){
                $profile["followers"] = 0;
            }
            if(is_null($profile["follows"])){
                $profile["follows"] = 0;
            }
        return response()->json([
            'username' => $profile["name"],
            'followers' => $profile["followers"],
            'follows' => $profile["follows"],
            'image' => $profile["image"],
            'message' => 'Usuario obtenido con éxito'
        ], 200);
    }
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
    ], 200);
}     

function login(Request $request){
    Log::debug('Login request received', ['request' => $request->all()]);

    $json = $request->getContent();
    $validator =  Validator::make(json_decode($json, true), [
        'name' => 'required',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        Log::warning('Validation error', ['errors' => $validator->errors()]);
        return response()->json([
            'errors' => $validator->errors(),
            'message' => 'Ha habido un error validando la información introducida.',
        ], 422);
    }else{

    try {
        $users = User::where('name', $validator->validated()['name'])->firstOrFail();
        if (!Hash::check($validator->validated()['password'], $users->password)) {
            Log::warning('Authentication error', ['username' => $validator->validated()['name']]);
            return response()->json([
                'message' => 'La contraseña es incorrecta.',
            ], 400);
        }
        $users->tokens()->delete();
        $token = $users->createToken($users->name)->plainTextToken;
        Log::info('User logged in', ['username' => $users->name]);
    } catch (Exception $e) {
        Log::error('Server error', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'Ha habido un error con la conexión.',
        ], 500);
    }
    }
    

    return response()->json([
        'user' => $users,
        'token' => $token,
        'message' => 'Te has logueado correctamente.',
    ], 200);
}



//POST /users/update/ID
public function update(Request $request){

        $datos = json_decode($json);

        if($datos){
            if(isset($datos->image, $datos->id)){

            $user = User::find($id);
            if (isset($user)){
 
                        $base64Image = $datos->image;
                     $decodedImage = base64_decode($base64Image);

                    // Create a temporary file to store the decoded image
                    $tempFile = tempnam(sys_get_temp_dir(), 'image');

                    // Write the decoded image to the temporary file
                    file_put_contents($tempFile, $decodedImage);

                    // Create a new UploadedFile instance from the temporary file
                    $uploadedFile = new \Illuminate\Http\UploadedFile($tempFile, $datos->name);

                    // Store the file in storage/app/public/images directory
                    $path = $uploadedFile->store('public/images');       
                    
                    $recipe->image = $path;
            
            
                try{
                $user->save();
                }catch(\Exception $e){
                    return response()->json([
                        'message' => 'error al guardar'
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => 'usuario inexistentes'
                ], 400);
            }
            } else {
            return response()->json([
                'message' => 'Parametros incorrectos'
            ], 400);
            }
        
        } else {
            return response()->json([
                'message' => 'JSON incorrecto'
            ], 400);
        }
        return response()->json([
            'message' => 'Imagen actualizada con éxito'
        ], 200);
    }
}
