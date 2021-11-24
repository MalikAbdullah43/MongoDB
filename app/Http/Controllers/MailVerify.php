<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\mail;
use App\Mail\TestMail;
use App\Service\ConnectionService;

class MailVerify extends Controller
{
    public function verify($email)
    {
        
            $connect    = new ConnectionService();
            $databse    = $connect->getdb();
            $collection = "users";

            $nosql = $databse->$collection->updateOne(
                ['email' => $email, 'email_verified_at' => null, 'link_expiry' => ['$gte' => now()->__toString()]],
                ['$set' => ['email_verified_at' => now()->__toString(), 'status' => 1]]
            );
            if (!empty($nosql->getModifiedCount())) {
                return "Successfully Verify";
            } else {
                return "Already Verified Or Link Expire";
            }
        }
    
    





    public function regenrate_link($email)
    {
        
            $connect    = new ConnectionService();
            $databse    = $connect->getdb();
            $collection = "users";
            $details = [
                'title' => 'This Social Application Verifacation',
                'link'  => 'http://127.0.0.1:8000/user/verification' . '/' . $email,
                'link1' => 'http://127.0.0.1:8000/user/regenrate' . '/' . $email
            ];
            //Mail Sending Facade
            Mail::to('malikabdullah4300@gmail.com')->send(new TestMail($details));

            //MongoDB Query
            $time = now()->addMinutes(10)->__toString();  //link_expiry Extend 
            $nosql = $databse->$collection->updateOne(
                ['email' => $email, 'status' => 0],
                ['$set' => ['link_expiry' => $time]]
            );
            //check if User Link is Valid regenerate link
            if ($nosql->getModifiedCount() > 0)
                return "Email Regenrate Successfully";
            else
                return "Link Already Verify";
        } 
    }

