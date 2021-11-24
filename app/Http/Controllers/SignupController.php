<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Service\ConnectionService;
use App\Service\MailService;
use App\Http\Requests\SignupRequest;

date_default_timezone_set("Asia/Karachi");
class SignupController extends Controller
{
   public $collection;

   public function __construct()
   {
      $connect    = new ConnectionService();
      $databse    = $connect->getdb();
      $this->collection = $databse->users;
   }
   function signUp(SignupRequest $req)
   {
         $req->validated();


         $mail       = new MailService();
         //Here We Create Instance of User Model For Passing Values in Model
         $find = $this->collection->findOne(['email' => $req->email]);

         if (empty($find->_id)) {

            if (!empty($req->file('image'))) {

               $results = $req->file('image')->store('apidoc');
               $insert = $this->collection->insertOne([
                  'name'     => $req->name,
                  'email'    => $req->email,
                  'password' => Hash::make($req->password),
                  'gender'   => $req->gender,
                  'image'    =>  $results,
                  'friends'=>array(),  
               ]);
            } else {


               $insert = $this->collection->insertOne([
                  'name'     => $req->name,
                  'email'    => $req->email,
                  'password' => Hash::make($req->password),
                  'gender'   => $req->gender,
                  'image'    => '',
                  'created_at' => now()->__toString(), //now(),//Date('Y-m-d h:i:s'),
                  'friends'=>array(),  

               ]);
            }
         } else {
            return response()->json(["Message" => "Email Already Exists", "Status" => "409"], 409);
         }
         $id =  $insert->getInsertedId()->__toString();
         if (!empty($id)) {
            if ($mail->mail($req->email)) {
               $time = now()->addMinutes(4)->__toString();
               $this->collection->updateOne(['email' => $req->email], ['$set' => ['email_verified_at' => null, 'status' => 0, 'link_expiry' => $time]]);
               return response()->json([
                  "Message" => "Account Created Successfully Kindly Verify Your Email",
                  "Status" => "200", "Data" => ["user_id" => $id]
               ], 200);
            } else {
               return response()->json(["Message" => "Not save", "Status" => "500"], 500);
            }
         }
      
      }
   }

