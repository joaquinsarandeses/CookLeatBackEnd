<?php
        namespace App\Http\Controllers;
        
        use Illuminate\Http\Request;
        use App\Models\Recipe;
        
        class RecipesController extends Controller
        {
             //GET /recipes/list?filtros
             public function list(Request $request){
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
        
            //GET /recipes/list/ID
            public function show($id){
                $response = [
                    "status" => "ok",
                    "code" => 10,
                    "data" => ""
                ];

                $recipe = Recipe::find($id);
                if ($recipe){
                    $response["data"] = $recipe;
                } else{
                    $response["status"] = "receta no existente";
                        $response["code"] = 14;

                }
                return response()->json($response);
            }


            
            //DELETE /recipes/delete
            public function delete($id){
                $recipe = Recipe::find($id);
                $recipe->delete();
        
            }

            //PUT /recipes/create
            public function create(Request $request){
                $response = [
                    "status" => "ok",
                    "code" => 10,
                    "data" => ""
                ];

                $json = $request->getContent();

                $datos = json_decode($json);

                if($datos){
                    if(isset($datos->name, $datos->image, $datos->status, $datos->class)){

                    $recipe = new Recipe();
                    $recipe->name = $datos->name;
                    $recipe->description = $datos->description;
                    $recipe->image = $datos->image;
                    $recipe->user_id = $datos->user_id;
                    $recipe->category_id = $datos->category_id;
                    

                    try{
                    $recipe->save();
                    $response["data"] = "ID: $recipe->id";
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