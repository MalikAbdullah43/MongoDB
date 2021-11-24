<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Middleware\UserVerify;



Route::middleware(['verify'])->group(function(){

    //Post Routes
    Route::post('postcreate',[PostController::class,"postCreate"]);  //Post Create
    Route::get('userposts',[PostController::class,"userPosts"]); //Post Create
    Route::post('postupdate/{pid}',[PostController::class,"postUpdate"]);  //Post Update
    Route::get('postdelete/{pid}',[PostController::class,"postDelete"]);  //Post Delete
    Route::post('search',[PostController::class,"postSearch"]);  //Post Search
});




