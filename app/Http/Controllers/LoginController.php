<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\mail;
use App\Mail\TestMail;
use App\Mail\LoginMail;
use Carbon\Carbon;
use App\Http\Requests\LoginRequest;
use App\Service\ConnectionService;
use App\Service\TokenService;
// use MongoDB\Client as test;


class LoginController extends Controller
{
    public $collection;

    public function __construct()
    {
        $connect    = new ConnectionService();
        $databse    = $connect->getdb();
        $this->collection = $databse->users;
    }

    /// If User Login Then This Function Call Response Send On User Email
    public static function login_mail($email)
    {
        try {
            $details = [
                'title' => 'Log in Confirmation Mail',
                'Message' => 'Your Are Log in At ' . now()
            ];

            Mail::to('malikabdullah4300@gmail.com')->send(new LoginMail($details));   //Here in to We Put Mail $req->email Where We send mail
            return "Email Send";
        } catch (\Exception $ex) {
            $response->set_error_response(null, $ex->getMessage(), "500", "Server error");
            return $response->error_respond_api();
        }
    }

    //Mail Function End

    //Log In Function Call

    public function logIn(LoginRequest $req)
    {
        try {

            $req->validated();
            $token = new TokenService();
            $Auth_key = $token->encode();
            if ($Auth_key != false) {

                $data = $token->decode();
                $password          = $data->data->password;  //Hashing Purpose
                $user_data['id']   = $data->data->_id;
                $user_data['email'] = $data->data->email;

                if (Hash::check($req->password, $password))  //If Password Match
                {
                    try { //Data Checking From Database user Exist or Not
                        $nosql = $this->collection->find(
                            [
                                'email' => $req->email,
                                'email_verified_at' => ['$ne' => null]
                            ]
                        );
                        $objects = json_decode(json_encode($nosql->toArray(), true));
                        $count = Count($objects);

                        if ($count > 0)  //If Verify Then This Code Execute
                        {


                            $time = now()->addMinutes(30)->__toString();  //Token Expiry Set
                            $nosql = $this->collection->updateOne(
                                ['email' => $req->email],
                                ['$set' => ['remember_token' => $Auth_key, 'updated_at' => $time]]
                            );

                            self::login_mail($req->email);
                            return response(["Message" => ["message" => "Successfully Login", "Status" => 200], 'Auth_key' => $Auth_key,], 200);
                        } else {
                            return response(["Message" => "Unauthorized Access or Verify Your Email", "Status" => "404"], 404);  //Message For Unauthorized user
                        }
                    } catch (\Exception $e) {
                        return array('error' => $e->getMessage());
                    }
                } else {

                    return response(["Message" => "Credentials Not Matched", "Status" => "404"], 404); //If User Input Not Match Then Through This Message
                }
            } else {
                return response(["Message" => "Credentials Not Matched", "Status" => "404"], 404);
            }
        } catch (\Exception $ex) {
            $response->set_error_response(null, $ex->getMessage(), "500", "Server error");
            return $response->error_respond_api();
        }
    }
}
