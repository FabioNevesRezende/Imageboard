<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    public $timestamps = false;
    
    public function posts(){
        return $this->hasMany('Ibbr\Post');
    }
}
