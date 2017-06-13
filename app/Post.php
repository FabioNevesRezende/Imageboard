<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    
    public function arquivos(){
        return $this->hasMany('Ibbr\Arquivo');
    }
    
    public function ban()
    {
        return $this->hasOne('Ibbr\Ban');
    }
    
}
