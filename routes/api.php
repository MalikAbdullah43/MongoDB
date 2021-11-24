<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;
use App\Http\Middleware\UserVerify;
use App\Http\Middleware\Validation;
use App\Http\Middleware\UpdateValidation;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\checkcontroller;

Route::get('check',[checkcontroller::class,'my']);


Route::middleware(['verify'])->group(function(){
 
    //Comment Routes
    Route::post('comment',[CommentController::class,"commentCreate"]);  //Comment Create
    Route::get('comment/delete/{cid}/{pid}',[CommentController::class,"commentDelete"]);  //Comment Delete
    Route::get('postcomments/{pid}',[CommentController::class,"postComments"]);  //Comment Delete
    //Friend Routes
    Route::post('/friend', [FriendController::class,"addFriends"]);
    Route::get('/friendlist', [FriendController::class,"showFriends"]);
    Route::post('/friend/remove/', [FriendController::class,"remove"]);
    
});




