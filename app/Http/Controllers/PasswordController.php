<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Hash;
use App\Mail\PasswordMail;
use Illuminate\Support\Facades\mail;
use Illuminate\Http\Request;
use App\Service\TokenService;
use App\Service\ConnectionService;

class PasswordController extends Controller
{

    public $collection;

    public function __construct()
    {
        $connect    = new ConnectionService();
        $databse    = $connect->getdb();
        $this->collection = $databse->users;
    }
    public function forgetPassword(ForgetPasswordRequest $req)
    {  
        $req->validated();
        $otp = rand(111111, 999999);
        //Token

        $user = $this->collection->findOne(['email' => $req->email]);  //Data Checking From Database user Exist or Not



        if (!empty($user->_id)) {
            $password          = $user->password;  //Hashing Purpose
            $user_data['id']   = $user->_id;
            $user_data['email'] = $user->email;

            //Token JWT END Payload

            $nosql = $this->collection->findOne(['$and' => [['email' => $req->email], ['email_verified_at' => ['$ne' => null]], ['status' => 1]]]);



            if (!empty($nosql))  //If Verify Then This Code Execute
            {
                $token = new TokenService();    //JWT Updation And Printing Message of Log in
                $Auth_key = $token->encode();
                $time = now()->addMinutes(30)->__toString();
                $user = $this->collection->updateOne(['email' => $req->email], ['$set' => ['remember_token' => $Auth_key, 'updated_at' => $time, 'otp' => $otp]]);
            }
        }

        //end
        //Token Validity Increase if All Activity Perfoam And Message Show
        if (self::mail($req->email, $otp)) {

            return response(
                [
                    "Message" => "Otp Send On Email", "Status" => "200", "Auth_key2" => $Auth_key
                ],
                200
            );
        }
    
   
}
    public function mail($email, $otp)
    { 
        $details = [
            'title' => 'Hello Dear User',
            'Message' => 'This is  Your Otp:' . $otp,
        ];


        Mail::to('malikabdullah4300@gmail.com')->send(new PasswordMail($details));
        return "Email Send";
    }

    //Reset Password
    public function passwordReset(ResetPasswordRequest $req)
    {
        $req->validated();
        $otp = $req->otp;
        $jwt = $req->bearerToken();
        $user = $this->collection->findOne([
            '$and' => [
                ['remember_token' => $jwt],
                ['updated_at' => ['$gte' => now()->__toString()]],
                ['otp' => (int)$otp],
            ]
        ]);

        if (!empty($user)) {

            $update = $this->collection->updateOne(
                ['_id' => $user->_id],
                ['$set' => [
                    'password' => Hash::make($req->new_password),
                    'remember_token' => ''
                ]]
            );

            if ($update->getModifiedCount() > 0) {
                return response(["Message" => "Password Change Succesfully", "Status" => "200"], 200);
            } else {
                return response(["Message" => "OTP Expire", "Status" => "404"], 404);
            }
        } else {
            return response(["Message" => "OTP Expire or Invalid Otp", "Status" => "404"], 404);
        }
    }
    
    }
   

