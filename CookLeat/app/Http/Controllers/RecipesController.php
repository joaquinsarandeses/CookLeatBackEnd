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
                    } else {
                        $recipe = Recipe::join('categories', 'recipes.category_id', '=', 'categories.id')
                        ->select('recipes.id', 'recipes.name', 'recipes.image', 'categories.name AS category', 'recipes.created_at')
                        ->where('recipes.category_id', $request->input('category_id'))
                        ->get();
                    }
                } else{
                    if($request->has('name')){
                        $recipe = Recipe::join('categories', 'recipes.category_id', '=', 'categories.id')
                        ->select('recipes.id', 'recipes.name', 'recipes.image', 'categories.name AS category', 'recipes.created_at')
                        ->where('recipes.name', 'LIKE', '%'.$request->input('name').'%')
                        ->get();
                    } else {
                $recipe = Recipe::all();
                }
            }
                return $recipe;
        }
        
            //GET /recipe/list/ID
            public function show(Request $request){

                $checkRecipe = Recipe::find($request->id);

                if(isset($checkRecipe)){
                    $recipe = Recipe::select('recipes.name', 'recipes.description', 'recipes.image', 'users.name as user', 'users.image as profilePicture', 'categories.name as category')
                    ->join('users', 'users.id', '=', 'recipes.user_id')
                    ->join('categories', 'categories.id', '=', 'recipes.category_id')
                    ->where('recipes.id', $request->id)
                    ->get();
                } else{
                    return response()->json([
                        'message' => 'Receta no encontrada'
                    ], 404);
                }
                return $recipe;
                return response()->json([
                    'message' => 'Image not found',
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
     public function favorite(Request $request){
        $checkRecipe = User::find($request->id);

        if(isset($checkRecipe)){


        $favorites = Recipe::select('recipes.*', 'users.name as user', 'users.image as profilePicture')
        ->join('favorites', 'recipes.id', '=', 'favorites.recipe_id')
        ->join('users', 'users.id', '=', 'favorites.user_id')
        ->where('favorites.user_id', $id)
        ->get();

        if($favorites->isNotEmpty()){
            foreach ($favorites as $favorite) { 
                $recipeRoute = $favorite['image'];
                $recipePath = storage_path('app/' . $recipeRoute);
                if (!file_exists($recipePath)) {
                    return response()->json(['message' => 'Image not found'], 404);
                 // return $recipePath;
                } else{
                    $file = file_get_contents($recipePath);
                    $encodedData = base64_encode($file);
                   // $encodedData = str_replace('+', '-', $encodedData);
                   // $encodedData = str_replace('/', '_', $encodedData);
                   // $encodedData = rtrim($encodedData, '=');
                    $favorite['image'] = base64_encode($file);
                   // return $favorite;
                }
                if (isset($userRoute)){
                    $userRoute = $favorite['profilePicture'];
                    $userPath = storage_path('app/' . $userRoute);
                if (!file_exists($userPath)) {
                    return response()->json([
                        'message' => 'Image not found'
                    ], 404);
                } else{
                    $favorite['profilePicture'] = file($userPath);
                    
                }
                }
         }
        } else {
            //Consulta general de recetas
        }
    }
    return response()->json([
        'favorites' => $favorites
    ], 200);
     }
}