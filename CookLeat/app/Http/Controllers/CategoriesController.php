<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;


class CategoriesController extends Controller
{
    //GET /categories/list/ID
    public function show($id){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $category = Category::find($id);
        if ($category){
            $response["data"] = $category;
        } else{
            $response["status"] = "categoría no encontrada";
            $response["code"] = 404;

        }
        return response()->json($response);
    }

    //GET /categories/list/
    public function list(){

        $category = Category::all();

        if($category->isNotEmpty()){
            return response()->json([
                'message' => 'Categorias devuelta con éxito',
                'categories' => $category
            ], 200);
        } else {
            return response()->json([
                'message' => 'No hay categorias creadas'
            ], 404);
        }
    }

    //DELETE /categories/delete
    public function delete($id){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $category = Category::find($id);
        $category->delete();
    }
}