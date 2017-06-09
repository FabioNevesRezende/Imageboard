<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'filename',
    ];
    
    public function post(){
        return $this->belongsTo('Ibbr\Post');
    }
}
