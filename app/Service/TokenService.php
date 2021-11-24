<?php
namespace App\Service;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class TokenService{    
 
public function encode()
   {
    
      //For JWT Token Code
      $payload = self::payload();
      if($payload){
      $key="Malik$43";
      $Auth_key = JWT::encode($payload, $key, 'HS256');    //JWT Updation And Printing Message of Log in
      if($Auth_key)
      return $Auth_key;
    }
      else
      return false;
    

   }
   public function  decode()
   {
    $payload = self::payload();
    $key="Malik$43";
    JWT::$leeway = 1800; // $leeway in seconds
    $jwt = JWT::encode($payload, $key, 'HS256');
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
    
    return $decoded;
   }



   public function payload()
   {

    $req = Request();
    $connect    = new ConnectionService();        
    $db         = $connect->getConnection();   
    $databse    = $connect->getdb(); 
    $collection = "users";
    $user = $db->$databse->$collection->findOne(['email'=>$req->email]);   //Data Checking From Database user Exist or Not
    if(!empty($user->_id)){
    $user['_id']=(string)$user->_id;
    $key="Malik$43";

    $payload= array(
        "iss" =>"localhost",
        "iat" =>time(), 
        "nbf" =>time()+10,
        "aud" =>"user", 
        "data" =>$user
    );
    return $payload;
    }
    else{
        
     return false;
    }


}
}