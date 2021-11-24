<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\PostController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

  
class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'access',
        'text',
        'file',
        'user_id ',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
    /**
     * The for database table.
     *
     * @var array
     */
    protected $table = 'posts';
    protected $attributes = [
        'file' => 'NULL',
        'access' =>'1',
    ];

//Relationship With User
    public function Users(){
        return $this->belongsTo(User::class);
    }
  // Relationship With Comment
    public function comments(){
        return $this->hasMany(Comment::class);
    }

}
