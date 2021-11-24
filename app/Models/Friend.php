<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Friend extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
  //  use SoftDeletes;z
    public function users() {
        return $this->belongsTo(User::class);
    }
    public function friends(){
        return $this->belongsTo(User::class);
    }

}
