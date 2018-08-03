<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $table = 'noticias';
    
    public function autor()
    {
        return $this->hasOne('Ibbr\User', 'id','autor_id');
    }
    
}
