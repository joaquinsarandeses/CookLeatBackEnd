<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class FollowsController extends Controller
{

    //DELETE /categories/delete
    public function delete($id){
        $response = [
            "status" => "ok",
            "code" => 200,
            "data" => ""
        ];

        $category = Category::find($id);

        
        $category->delete();
    }//PUT /users/create
    public function create(Request $request){

    }
}