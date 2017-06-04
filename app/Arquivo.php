<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    
    protected $fillable = [
        'filename', 'filepath',
    ];
    
    public function post(){
        return $this->belongsTo('Ibbr\Post');
    }
}
