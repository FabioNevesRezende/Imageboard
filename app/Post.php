<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    
    public function arquivos(){
        return $this->hasMany('Ibbr\Arquivo');
    }
    
    public function ytanexos(){
        return $this->hasMany('Ibbr\Ytanexo');
    }
    
    public function ban()
    {
        return $this->hasOne('Ibbr\Ban');
    }
    
}
