<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\mail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Http\Requests\UpdateProfileRequest;
use App\Service\ConnectionService;

header('content-type: application/json');

class UserController extends Controller
{

  //For Updation User Data
  public function edit(UpdateProfileRequest $req)
  {
    try {
      $req->validated();
      $connect    = new ConnectionService();
      $database    = $connect->getdb();
      $collection = "users";

      $jwt = $req->bearerToken();
      $data_to_update = [];
      foreach ($req->all() as $key => $value) {
        if (in_array($key, ['name', 'password', 'gender'])) {
          $data_to_update[$key] = $value;
        }
      }
      if (!empty($data_to_update['password']))
        $data_to_update['password'] = Hash::make($data_to_update['password']);

      if (!empty($req->image)) {
        $result = $req->file('image')->store('apidoc'); //Image Saving
        $data_to_update['image'] = $result;
      }

      $update = $database->$collection->updateOne(['remember_token' => $jwt], ['$set' => $data_to_update]);

      if ($update->getModifiedCount() > 0) {
        $time = now()->addMinutes(30)->__toString();  //Token Expiry Set 
        $update = $database->$collection->updateOne(
          ['remember_token' => $jwt], //Token Validity Increase if All Activity Perfoam And Message Show
          ['$set' => ['updated_at' => $time]]
        );

        return response(
          [
            "Message" => "Data Update Successfully", "Status" => "200"
          ],
          200
        );
      } else {
        return response(
          [
            "Message" => "Data Not Update", "Status" => "500"
          ],
          500   //If any Error Occure Then Error Show
        );
      }
    } catch (Exception $ex) {
      $response->set_error_response(null, $ex->getMessage(), "500", "Server error");
      return $response->error_respond_api();
    }
  }


  public function logOut(Request $req)
  {
    try {
      $jwt = $req->bearerToken();
      $connect    = new ConnectionService();
      $database    = $connect->getdb();
      $collection = "users";
      $nosql = $database->$collection->updateOne(['remember_token' => $jwt], ['$set' => ['remember_token' => '']]);

      if ($nosql->getModifiedCount() > 0)
        return response(['Message' => 'Successfully Logout', 'Status' => '200'], 200);

      else
        return response(['Message' => 'May Be SomeThing Wrong!!', 'Status' => '404'], 404);
    } catch (\Exception $ex) {
      $response->set_error_response(null, $ex->getMessage(), "500", "Server error");
      return $response->error_respond_api();
    }
  }
}
