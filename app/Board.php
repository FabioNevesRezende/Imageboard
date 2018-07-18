<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $table = 'boards';
    public $timestamps = false;
    
    public function posts(){
        return $this->hasMany('Ibbr\Post');
    }
}
