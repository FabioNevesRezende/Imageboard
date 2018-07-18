<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    public $timestamps = false;
    protected $table = 'arquivos';
    
    protected $fillable = [
        'filename', 'mime',
    ];
    
    public function post(){
        return $this->belongsTo('Ibbr\Post');
    }
}
