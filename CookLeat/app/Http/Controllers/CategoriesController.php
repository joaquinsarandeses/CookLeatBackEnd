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
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $category = Category::all();

        $response["data"] = $category;
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