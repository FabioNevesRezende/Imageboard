<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Ytanexo extends Model
{
    protected $table = 'ytanexos';
    public $timestamps = false;
    
    protected $fillable = [
        'ytcode', 'post_id',
    ];
    
    public function post(){
        return $this->belongsTo('Ibbr\Post');
    }
}
