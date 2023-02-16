<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\RecipesController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('/categories')->group(function(){
    Route::get('/list',[CategoriesController::class,'list']); //ver lista categorías
    Route::get('/list/{id}',[CategoriesController::class,'show']); //ver categoría
    Route::delete('/delete',[CategoriesController::class,'delete']); //Crear categoría
});

Route::prefix('/favourite')->group(function(){
    Route::put('/create',[FavoritesController::class,'create']); //Crear relacion 

    Route::delete('/delete',[FavoritesController::class,'delete']); //Borrar relacion 
});

Route::prefix('/users')->group(function(){
    Route::put('/create',[UsersController::class,'create']); //Crear Users
    Route::post('/update/{id}',[UsersController::class,'update']); //actualizar Users
    Route::get('/list',[UsersController::class,'list']); //ver lista Users
    Route::get('/list/{id}',[UsersController::class,'show']); //ver user
});

Route::prefix('/follow')->group(function(){
    Route::put('/create',[FollowsController::class,'create']); //Crear relacion 

    Route::delete('/delete',[FollowsController::class,'delete']); //Borrar relacion 
});


Route::prefix('/recipe')->group(function(){
    Route::put('/create',[RecipesController::class,'create']); //Crear receta
    Route::get('/list',[RecipesController::class,'list']); //ver lista receta
    Route::get('/list/{id}',[RecipesController::class,'show']); //ver receta
    Route::delete('/delete',[RecipesController::class,'delete']); //Crear receta
});

