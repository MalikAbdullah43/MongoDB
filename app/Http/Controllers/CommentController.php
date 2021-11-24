<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Service\ConnectionService;

class CommentController extends Controller
{
    public $collection;

    public function __construct()
    {
        $connect    = new ConnectionService();
        $databse    = $connect->getdb();
        $this->collection = $databse->posts;
        $this->collection1 = $databse->users;
    }



    public function commentCreate(CreateCommentRequest $req)
    {
        try {
            $req->validated();
            //Finding Active User ID
            $token = request()->bearerToken();
            $secret_key = "Malik$43";
            $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));

            $pid = new \MongoDB\BSON\ObjectId($req->postId);
            //End
            //If User Want To Post Comment Own Post Then They Directly Post With No Restrictions
            $id  = $decoded->data->_id;
            $ownPost =  $this->collection->findOne(['user_id' => $id, '_id' => $pid, 'deleted_at' => null]);

            if (!empty($ownPost)) {
                $pid = new \MongoDB\BSON\ObjectId($req->postId);
                //
                $comment = array(
                    "uuid" => new \MongoDB\BSON\ObjectId(),
                    "user_id" => $id,
                    "comment" => $req->comment,
                    "deleted_at" => null,
                );

                $push = $this->collection->updateOne(["_id" => $pid], ['$push' => ["comments" => $comment]]);

                //
                if ($push->getModifiedCount() > 0)

                    return response(["Message" => "Your Own Post Comment Successfully", "Status" => "200"], 200);
                else
                    return response(["Message" => "Error Occure", "Status" => "500"], 500);
            }
            ///User Own Post End
            //Check Other Conditions
            $access =  $this->collection->findOne(['_id' => $pid, 'access' => '1', 'deleted_at' => null]);
            //If Post Is Public And Not Deleted Then Just Friends Comments on Post

            $user   = new \MongoDB\BSON\ObjectId($decoded->data->_id);
            $friends =  new \MongoDB\BSON\ObjectId($access->user_id);
            $postid = new \MongoDB\BSON\ObjectId($req->postId);
            //If Log in User is Friend of Which user own This Post Then They Allow For Comment
            $access =  $this->collection1->findOne(['_id' => $friends, 'friends.uuid' => $user, 'friends.deleted_at' => null]);
            if (!empty($access)) {
                if (empty($req->file)) {
                    $data_array1 =   array([
                        "uuid" => new \MongoDB\BSON\ObjectId(),
                        'comment' => $req->comment,
                        'user_id' => $user,
                        "deleted_at" => null,
                    ]);
                    $push =  $this->collection->updateOne(['_id' => $postid], ['$push' => ['comments' => $data_array1]]);
                } else {
                    $results = $req->file('file')->store('commentfiles');
                    $data_array1 =   array([
                        "uuid" => new \MongoDB\BSON\ObjectId(),
                        'comment' => $req->comment,
                        'user_id' => $user,
                        'file'  => $results,
                        "deleted_at" => null,
                    ]);
                    $push =  $this->collection->updateOne(['_id' => $postid], ['$push' => ['comments' => $data_array1]]);
                }
                if ($push->getModifiedCount() > 0)

                    return response(["Message" => "Your Comment Post Successfully", "Status" => "200"], 200);
                else
                    return response(["Message" => "Error Occure", "Status" => "500"], 500);
            }
        } catch (\Exception $ex) {
            $response->set_error_response(null, $ex->getMessage(), "500", "Server error");
            return $response->error_respond_api();
        }
    }


    ///Comment Deletion End Point
    public function commentDelete(Request $req)
    {
        try {
            $token = request()->bearerToken();
            $secret_key = "Malik$43";
            $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
            $user = $decoded->data->_id;
            $cid = new \MongoDB\BSON\ObjectId($req->cid);
            $pid = new \MongoDB\BSON\ObjectId($req->pid);

            //   $select  = DB::table('comments')->where(["id"=>$cid,'user_id'=> $user,'post_id'=> $pid])->whereNull('deleted_at')->get();
            $time = now()->__toString();

            $comment = $this->collection->updateOne(
                ['_id' => $pid, 'comments.uuid' => $cid, 'comments.user_id' => $user, 'comments.deleted_at' => null],
                ['$set' => ['comments.$.deleted_at' => $time]]
            );

            if ($comment->getModifiedCount() > 0) {
                return response(["Message" => "Successfully Comment Delete", "Status" => "200"], 200);
            } else
                return response(["Message" => "Comment Not Exist", "Status" => "404"], 404);
        } catch (\Exception $ex) {
            $response->set_error_response(null, $ex->getMessage(), "500", "Server error");
            return $response->error_respond_api();
        }
    }
}
