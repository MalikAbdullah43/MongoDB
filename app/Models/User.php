<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\UserController;
use App\Models\Post;
  
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'status',
        'email_verified_at',
        'count',
        'updated_at',
        'otp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    public function Post(){
        return $this->hasMany(Post::class);
    }
    //FriendShip Relationships
    public function users() {
        return $this->hasMany(Friend::class);
    }
    public function friends() {
        return $this->hasMany(Friend::class);
    }
    //Comment Relationship
    public function comments() {
        return $this->hasMany(Comment::class);
    }
}
