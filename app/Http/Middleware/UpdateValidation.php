<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Validator; 

class UpdateValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $req, Closure $next)
    {
        $validator = Validator::make($req->all(),[ 
            'name'    => 'required|string',
            'password'=> [
                'required',
                'string',
                'min:8',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'gender'  => 'required',
            'image'=>'required|mimes:png,jpg,jpeg,gif|max:2305'
      ]);

   //If Any Error Occur in Validation Then Show in console 

    if($validator->fails()){          
             return response()->json(['error'=>$validator->errors()], 401);                        
     }  
        return $next($req);
    }
}
