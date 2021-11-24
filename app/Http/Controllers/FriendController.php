<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use App\Service\ConnectionService;

class FriendController extends Controller
{
  
  public $collection;

  public function __construct()
  {
    $connect    = new ConnectionService();
    $databse    = $connect->getdb();
    $this->collection = $databse->users;
  }

  public function addFriends(Request $req)
  {
    
      $secret_key = "Malik$43";
      $token = request()->bearerToken();
      $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
      $uid = new \MongoDB\BSON\ObjectId($decoded->data->_id);
      $fid = new \MongoDB\BSON\ObjectId($req->friend_id);

      $user = $this->collection->findOne(['_id' => $fid, 'email_verified_at' => ['$ne' => null]]);
      if (!empty($user)) {
        $check = $this->collection->findOne(['_id' => $uid, 'email_verified_at' => ['$ne' => null]]);
        $array = $check['friends'];
        $cond = true;

        foreach ($array as $key) {
          if ($fid == $key['uuid'] && !empty($key['deleted_at']))
            $cond = true;
          else
            $cond = false;

          if ($fid != $key['uuid']) {
            $cond = true;
          }
        }


        if ($cond == true) {

          if ($uid == $fid) {
            return response()->json(["Message" => "User Cannot Add Yourself", "Status" => "404"], 404);
          }
          $firends = array(
            'uuid' => new \MongoDB\BSON\ObjectId($fid),
            'deleted_at' => null,
          );
          $firends2 = array(
            'uuid' => new \MongoDB\BSON\ObjectId($uid),
            'deleted_at' => null,
          );

          $push = $this->collection->updateOne(['_id' => $uid], ['$push' => ["friends" => $firends]]);
          $user = $this->collection->updateOne(['_id' => $fid], ['$push' => ["friends" => $firends2]]);
          if ($push->getModifiedCount() > 0 && $user->getModifiedCount() > 0)
            return response()->json(["Message" => "Friend Add Successfully", "Status" => "200"], 200);

          else
            return response()->json(["Message" => "Something Wrong", "Status" => "500"], 500);
        } else
          return response()->json(["Message" => "This User Already Your Friend", "Status" => "409"], 409);
      } else
        return response()->json(["Message" => "This User Could not Exists", "Status" => "404"], 404);
    
    }
  



  //List Friends
  public function showFriends()
  {
    try {
      $secret_key = "Malik$43";
      $token = request()->bearerToken();
      $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
      $fid = new \MongoDB\BSON\ObjectId($decoded->data->_id);
      $check = $this->collection->findOne(['_id' => $fid, 'friends.$.deleted_at' => null]);
      return response($check->friends);
    } catch (\Exception $ex) {
      $response->set_error_response(null, $ex->getMessage(), "500", "Server error");
      return $response->error_respond_api();
    }
  }

  public function remove(Request $req)
  {
    try {
      $secret_key = "Malik$43";
      $token = request()->bearerToken();
      $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
      $uid = new \MongoDB\BSON\ObjectId($decoded->data->_id);
      $fid = new \MongoDB\BSON\ObjectId($req->friend_id);
      $time = now()->__toString();
      $find = $this->collection->find(['_id' => $uid, 'friends.uuid' => $fid, 'friends.$.deleted_at' => null]);
      return $find->toArray();
      if (!empty($find->toArray())) {

        $friend = $this->collection->updateMany(['_id' => $uid, 'friends.$.uuid' => $fid, 'friends.$.deleted_at' => null], ['$set' => ['friends.$.deleted_at' => $time]]);
        $friend = $this->collection->updateMany(['_id' => $fid, 'friends.$.uuid' => $uid, 'friends.$.deleted_at' => null], ['$set' => ['friends.$.deleted_at' => $time]]);
        if ($friend->getModifiedCount() > 0) {
          return response()->json(['Friend Remove' => $req->friend_id, 'Status' => "200"], 200);
        } else {
          return response()->json(['Message' => "Something Wrong", "Status" => "500"], 500);
        }
      } else return response()->json(["Message" => "User Not Exists", "Status" => "404"], 404);
    } catch (\Exception $ex) {
      $response->set_error_response(null, $ex->getMessage(), "500", "Server error");
      return $response->error_respond_api();
    }
  }
}
