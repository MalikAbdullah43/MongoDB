<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MailVerify;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\PasswordController;
use App\Http\Middleware\UserVerify;
use App\Http\Middleware\Validation;
use App\Http\Middleware\UpdateValidation;


//User Routes
Route::get('verification/{email}',[MailVerify::class,"verify"]);                        //Link Verification Route
Route::get('regenrate/{email}',[MailVerify::class,"regenrate_link"]);                   //Verirfy Link Re Create Route

Route::post('login',[LoginController::class,"logIn"]);                                 //Login Route
Route::post('signup',[SignupController::class,"signUp"]);          //Sign Up Route
//Password Routes
Route::post('forgetpassword',[PasswordController::class,"forgetPassword"]);          //Forget Route

Route::middleware(['verify'])->group(function(){
    //User Routes
    Route::post('logout',[UserController::class,"logOut"]);    //Log Out Route
    Route::post('uedit',[UserController::class,"edit"]);
    //Password Reset Request Identify
    Route::post('resetpassword',[PasswordController::class,"passwordReset"]);          //Password Reset Route
});




