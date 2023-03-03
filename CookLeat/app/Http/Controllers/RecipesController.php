<?php
        namespace App\Http\Controllers;
        
        use Illuminate\Http\Request;
        use App\Models\Recipe;
        use App\Models\User;
        use App\Models\Favorite;
        use App\Models\Category;
        
        class RecipesController extends Controller
        {
             //GET /recipe/list?filtros
             public function filter(Request $request){
                if($request->has('category_id')){
                    if($request->has('name')){
                        $recipe = Recipe::join('categories', 'recipes.category_id', '=', 'categories.id')
                        ->select('recipes.id', 'recipes.name', 'recipes.image', 'categories.name AS category', 'recipes.created_at')
                        ->where('recipes.category_id', $request->input('category_id'))
                        ->where('recipes.name', 'LIKE', '%'.$request->input('name').'%')
                        ->get();                      
                        if($recipe->IsNotEmpty()){
                            $recipe = getImages($recipe);
                        } else {
                            $recipe = Recipe::all();
                            $recipe = getImages($recipe);
                        }
                    } else {
                        $recipe = Recipe::join('categories', 'recipes.category_id', '=', 'categories.id')
                        ->select('recipes.id', 'recipes.name', 'recipes.image', 'categories.name AS category', 'recipes.created_at')
                        ->where('recipes.category_id', $request->input('category_id'))
                        ->get();
                        if($recipe->IsNotEmpty()){
                            $recipe = getImages($recipe);
                        } else {
                            $recipe = Recipe::all();
                            $recipe = getImages($recipe);
                        }
                    }
                } else{
                    if($request->has('name')){
                        $recipe = Recipe::join('categories', 'recipes.category_id', '=', 'categories.id')
                        ->select('recipes.id', 'recipes.name', 'recipes.image', 'categories.name AS category', 'recipes.created_at')
                        ->where('recipes.name', 'LIKE', '%'.$request->input('name').'%')
                        ->get();
                        if($recipe->IsNotEmpty()){
                            $recipe = getImages($recipe);
                        } else {
                            $recipe = Recipe::all();
                            $recipe = getImages($recipe);
                        }
                    } else {
                $recipe = Recipe::all();
                $recipe = getImages($recipe);
                }
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
                        $recipe = Recipe::select('recipes.name', 'recipes.description', 'recipes.image', 'users.name as user', 'users.image as profilePicture', 'categories.name as category')
                        ->join('users', 'users.id', '=', 'recipes.user_id')
                        ->join('categories', 'categories.id', '=', 'recipes.category_id')
                        ->where('recipes.id', $id)
                        ->get();
                        $recipe = getImages($recipe);
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

                        $recipe = new Recipe();
                        $recipe->name = $datos->name;
                        $recipe->description = $datos->description;
                        $recipe->image = $path;
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

            if($favorites->isNotEmpty()){
                $favorites = getImages($favorites);
            } else {
                $favorites = Recipe::select('recipes.*', 'users.name as user', 'users.image as profilePicture', 'categories.name as category')
                ->join('users', 'users.id', '=', 'recipes.user_id')
                ->join('categories', 'categories.id', '=', 'recipes.category_id')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
            if($favorites->isNotEmpty()){
                $favorites = getImages($favorites);
            } else {
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

        //GET recipe/recent/id
        public function recent(){

            $recent = Recipe::select('recipes.*', 'users.name as user', 'users.image as profilePicture', 'categories.name as category')
                ->join('users', 'users.id', '=', 'recipes.user_id')
                ->join('categories', 'categories.id', '=', 'recipes.category_id')
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

            if($recent->isNotEmpty()){
                $recent = getImages($recent);
            } else {
                return response()->json([
                    'message' => 'No hay recetas creadas'
                ], 404);
            }
            
            return response()->json([
                'recent' => $recent
            ], 200);
        }
}