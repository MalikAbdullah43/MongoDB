<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'comment',
        'file',
        'user_id ',
        'post_id',
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
     public function users() {
         return $this->belongsto(User::class);
     }
     public function posts() {
         return $this->belongsto(Post::class);
     }
}
