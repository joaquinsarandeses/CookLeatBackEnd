<?php
        namespace App\Http\Controllers;
        
        use Illuminate\Http\Request;
        use App\Models\Recipe;
        use App\Models\User;
        use App\Models\Favorite;
        use App\Models\Category;
        use Aws\S3\S3Client;
        use Illuminate\Support\Facades\Storage;
        
        class RecipesController extends Controller
        {
             //GET /recipe/list?filtros
             public function filter(Request $request){
                
                $recipe = Recipe::select('recipes.*', 'users.name as user', 'users.image as profilePicture', 'categories.name as category')
                ->join('users', 'users.id', '=', 'recipes.user_id')
                ->join('categories', 'categories.id', '=', 'recipes.category_id');
                
                if($request->has('category_id')){
                    $recipe->where('recipes.category_id', $request->input('category_id'));
                }
                if($request->has('name')){
                    $recipe->where('recipes.name', 'LIKE', '%'.$request->input('name').'%');
                 }
                 $recipe = $recipe->get();                    
                        if($recipe->isEmpty()){
                            return response()->json([
                                'message' => "No hay recetas disponibles"
                            ], 404);
                        }

                return response()->json([
                    'finder' => $recipe
                ], 200);
            }
            
            //GET /recipe/list/ID
            public function show($id){

                    $checkRecipe = Recipe::find($id);

                    if(isset($checkRecipe)){
                        try{
                        $recipe = Recipe::select('recipes.name', 'recipes.description', 'recipes.id as recipe_id', 'recipes.image', 'users.name as user', 'users.id as user_id', 'users.image as profilePicture', 'categories.name as category')
                        ->join('users', 'users.id', '=', 'recipes.user_id')
                        ->join('categories', 'categories.id', '=', 'recipes.category_id')
                        ->where('recipes.id', $id)
                        ->get();
                        } catch (Exception $e) {
                            return response()->json([
                                'message' => 'Fallo al obtener receta'
                            ], 404);
                        }
                    } else{
                        return response()->json([
                            'message' => 'Receta no encontrada'
                        ], 404);
                    }
                    return response()->json([
                        'recipe' => $recipe
                    ], 200);
            }
  
            //DELETE /recipe/delete
            public function delete($id){
                    $recipe = Recipe::find($id);
                    $recipe->delete();
            
            }

            
            //PUT /recipe/create
             public function create(Request $request){
                    $json = $request->getContent();

                    $datos = json_decode($json);

                    if($datos){
                        if(isset($datos->name, $datos->image, $datos->description, $datos->user, $datos->category)){

                            $image_path = 'images/'.$datos->user.'/'.$datos->name.'recipe';
                            $image_url = Storage::disk('s3')->put($image_path, base64_decode($request->image), 'public');
                            $url = Storage::disk('s3')->url($image_path);
                            $datos->image = $url;

                        $recipe = new Recipe();
                        $recipe->name = $datos->name;
                        $recipe->description = $datos->description;
                        $recipe->image = $url;
                        $recipe->user_id = $datos->user;
                        $recipe->category_id = $datos->category;
                        

                        try{
                        $recipe->save();
                        $response["data"] = "ID: $recipe->id";
                        }catch(\Exception $e){
                            return response()->json([
                                'message' => 'error al guardar'
                            ], 400);
                        }
                    } else{
                            return response()->json([
                                'message' => 'Faltan paramétros'
                            ], 400);
                        }

                    } else {
                        return response()->json([
                            'message' => 'Formato JSON incorrecto'
                        ], 400);
                    }     
                    return response()->json([
                        'message' => 'Receta creada con éxito'
                    ], 200);
            }          
            
            //GET recipe/favorite/id
            public function favorite($id){
                $checkUser = User::find($id);

                if(isset($checkUser)){


                $favorites = Recipe::select('recipes.*', 'users.name as user', 'users.image as profilePicture', 'categories.name as category')
                ->join('favorites', 'recipes.id', '=', 'favorites.recipe_id')
                ->join('users', 'users.id', '=', 'recipes.user_id')
                ->join('categories', 'categories.id', '=', 'recipes.category_id')
                ->where('favorites.user_id', $id)
                ->get();

                if($favorites->isEmpty()){
                    $favorites = Recipe::select('recipes.*', 'users.id as user_id', 'users.name as user', 'users.image as profilePicture', 'categories.name as category')
                    ->join('users', 'users.id', '=', 'recipes.user_id')
                    ->join('categories', 'categories.id', '=', 'recipes.category_id')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();
                if($favorites->isEmpty()){
                    return response()->json([
                        'message' => "No hay ninguna receta creada"
                    ], 400);
                }
                }
                } else{
                    return response()->json([
                        'message' => "fallo al obtener el usuario"
                    ], 400);
                }
                    return response()->json([
                        'favorites' => $favorites
                    ], 200);
            }

            //GET recipe/recent/
            public function recent(){

                $recent = Recipe::select('recipes.*', 'users.name as user', 'users.image as profilePicture', 'categories.name as category')
                    ->join('users', 'users.id', '=', 'recipes.user_id')
                    ->join('categories', 'categories.id', '=', 'recipes.category_id')
                ->orderBy('id', 'desc')
                ->limit(8)
                ->get();

                if($recent->isEmpty()){
                    return response()->json([
                        'message' => 'No hay recetas creadas'
                    ], 404);
                }
                
                return response()->json([
                    'recent' => $recent
                ], 200);
            }

            //GET recipe/list
            public function list(){

                $list = Recipe::select('recipes.*', 'users.name as user', 'users.image as profilePicture', 'categories.name as category')
                    ->join('users', 'users.id', '=', 'recipes.user_id')
                    ->join('categories', 'categories.id', '=', 'recipes.category_id')
                    ->orderBy('id', 'desc')
                ->get();

                if($list->isEmpty()){
                    return response()->json([
                        'message' => 'No hay recetas creadas'
                    ], 200);
                }
                
                return response()->json([
                    'list' => $list
                ], 200);
            }
}