<?php
namespace App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\mail;
use App\Mail\TestMail;
use App\Mail\LoginMail;

class MailService{    

    public function mail($email)
    {
        $details = ['title'=>'Hello Malik',
                    'link' =>'http://127.0.0.1:8000/user/verification'.'/'.$email,
                    'link1' => 'http://127.0.0.1:8000/user/regenrate'.'/'.$email];
                    Mail::to('malikabdullah4300@gmail.com')->send(new TestMail($details));
                    return "Email Send";     
    }
       
    
}