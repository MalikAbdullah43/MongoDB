<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Http\Requests\PostRequest;
use App\Service\ConnectionService;

class PostController extends Controller
{
    public $collection;

    public function __construct()
    {
        $connect    = new ConnectionService();
        $databse    = $connect->getdb();
        $this->collection = $databse->posts;
    }
    //This Function For Create Post
    public function postCreate(PostRequest $req)
    {
        
        $req->validated();
        $token = request()->bearerToken();
        $secret_key = "Malik$43";

        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        $data_to_update = [];

        foreach ($req->all() as $key => $value) {
            if (in_array($key, ['text', 'access'])) {
                $data_to_update[$key] = $value;
            }
        }
        $user_id = $decoded->data->_id;
        $data_to_update['user_id'] = $user_id;
        if (!empty($req->file('file'))) {
            $result = $req->file('file')->store('userposts');
            $data_to_update['file'] = $result;
        }
        $nosql = $this->collection->insertOne($data_to_update);
        
        if (!empty($nosql))
            return response(["Message" => "Successfully Created Posts", "Status" => "200"], 200);
        else
            return response(["Message" => "Error Occure in Post Create Posts", "Status" => "404"], 404);
    }
   

    

    //This Function Use For Check Which posts User Post
    public function userPosts(Request $request)
    {
       
        $token = request()->bearerToken();
        $secret_key = "Malik$43";
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));



        $user = $this->collection->find(['user_id' => $decoded->data->_id, 'deleted_at' => null]);
        return response()->json($user->toArray());
       
      
    }
    //This Function For User Which Post User Want to Update
    public function postUpdate(PostRequest $req)
    {  
        $req->validated();
        $postId = $req->pid;
        $token = request()->bearerToken();
        $secret_key = "Malik$43";
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        $connect    = new ConnectionService();
        $databse    = $connect->getdb();
        $collection = "posts";
        $id = new \MongoDB\BSON\ObjectId($postId);

        $posts = $this->collection->find(['_id' => $id, 'deleted_at' => null]);
        $userPost = $posts->toArray();
        $dataArray = [];
        if (!empty($userPost)) {
            $dataArray['text'] = $req->text;
            $dataArray['user_id'] = $decoded->data->_id;
            $dataArray['access'] = $req->access;

            if (!empty($req->file('file'))) {
                $result = $req->file('file')->store('userposts');
                $dataArray['file'] = $result;
            }

            $post = $this->collection->updateOne(['_id' => $id, 'user_id' => $decoded->data->_id], ['$set' => $dataArray]);

            if ($post->getModifiedCount() > 0)
                return response(["Message" => "Successfully Update Post", "Status" => "200",], 200);
        } else {

            return response(["Message" => "Post Not Found", "Status" => "404",], 404);
        }
    }
    
    



    //This Function For Delete Post
    public function postDelete(Request $req)
    { 
       
        $token = request()->bearerToken();
        $secret_key = "Malik$43";
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        $user = $decoded->data->_id;
        $id = new \MongoDB\BSON\ObjectId($req->pid);
        $time = now()->addMinutes(30)->__toString();
        $post = $this->collection->findOne(['_id' => $id, 'deleted_at' => null]);
        if (!empty($post)) {
            $post = $this->collection->updateOne(['_id' => $id], ['$set' => ['deleted_at' => $time]]);

            if ($post->getModifiedCount() > 0) {
                return response(["Message" => "Successfully Post Delete", "Status" => "200"], 200);
            } else
                return response(["Message" => "Post Not Found", "Status" => "404"], 404);
        } else
            return response(["Message" => "Post Not Found", "Status" => "404"], 404);}
        
    
    //Post Search
    public function postSearch(Request $request)
    {
        
        // Get the search value from the request
        $search = $request->search;
        // Search in the title and body columns from the posts collectons
        $match = $this->collection->find(['$text' => ['$search' => $search], 'access' => '1', 'deleted_at' => null]);
        // Return the search with the resluts
        if (!empty($match))
            return response($match->toArray());
        // // Return the if Not Found Any Post
        else
            return response(["Message" => "Result Not Found", "Status" => "404"], 404);
    }
}

